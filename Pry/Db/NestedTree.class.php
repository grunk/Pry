<?php

/**
 * Pry Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * 
 */

namespace Pry\Db;

/**
 * Classe Représentation intervallaire
 *
 * Permet la gestion de table à représentation intervallaire
 * 
 * <code>
 * $interval = new NestedTree('category');
 * $interval->setDB($objetZendDBAdapter);
 * echo $interval->getHTMLTree();
 * $interval->setCurrent(5);
 * $interval->addChild('Fils de 5');
 * $internal->getChilds(); // Retourne tous les enfant de 5
 * $interval->getChilds(Db_NestedTree::LEAFONLY); // Retourne tous les enfant de 5 qui ne sont pas des noeuds
 * </code>
 *
 * @category Pry
 * @package Db
 * @version 0.9.9 
 * @author Olivier ROGER <oroger.fr>
 *        
 * @todo suppression de noeud
 */
class NestedTree
{

    /**
     * Objet base de données
     * @var Zend_Db_Adapter_Abstract
     */
    protected $oSql = null;

    /**
     * Nom de la catégorie
     * @var string
     */
    protected $label;

    /**
     * Nom du champs de limite gauche
     * @var string
     */
    protected $leftBound;

    /**
     * Nom du champs de limite droit
     * @var string
     */
    protected $rightBound;

    /**
     * Nom de la table
     * @var string
     */
    protected $tableName;

    /**
     * Nom du champs de profondeur
     * @var string
     */
    protected $level;

    /**
     * Nom du champs id
     * @var string
     */
    protected $id;

    /**
     * Tableau contenant les bornes et le niveau de l'élément courant
     * @var array
     */
    protected $current;

    const LEAFONLY = 1;
    const NODEONLY = 2;

    /**
     * Initialise la représentation
     *
     * @param string $table nom de la table
     * @param string $label Nom du champs label
     * @param string $left Nom du champs de borne gauche
     * @param string $right Nom du champs de borne droite
     * @param string $level Nom du champs du niveau de profondeur
     * @param string $id Nom du champs id
     * @access public
     */
    public function __construct($table, $label = 'nom', $left = 'left', $right = 'right', $level = 'level', $id = 'id')
    {
        $this->tableName  = $table;
        $this->leftBound  = $left;
        $this->rightBound = $right;
        $this->level      = $level;
        $this->id         = $id;
        $this->label      = $label;
        $this->current    = array();
    }

    public function setDB(Zend_Db_Adapter_Abstract $db)
    {
        $this->oSql = $db;
    }

    /**
     * Défini l'élément de travail
     *
     * @param int $id Id de l'élément
     * @access public
     * @return string Nom de l'élément courant
     */
    public function setCurrent($id)
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $prepare = $this->oSql->prepare('SELECT `' . $this->leftBound . '`,`' . $this->rightBound . '`,`' . $this->level . '`,`' . $this->label . '` 
									  FROM ' . $this->tableName . ' 
									  WHERE `' . $this->id . '` =:id');
        $prepare->execute(array(':id' => $id));
        $current                = $prepare->fetch(PDO::FETCH_ASSOC);
        $this->current['left']  = intval($current[$this->leftBound]);
        $this->current['right'] = intval($current[$this->rightBound]);
        $this->current['level'] = intval($current[$this->level]);
        return $current[$this->label];
    }

    /**
     * Ajoute un élément racine.
     * Permet de débuter un arbre
     *
     * @param string $nom Nom de l'élément
     * @access public
     * @return int Id de l'élément
     */
    public function addRootElement($nom)
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        //Existe il déjà un élément root ?
        $data = $this->oSql->query('SELECT `' . $this->rightBound . '` FROM ' . $this->tableName . ' WHERE `' . $this->leftBound . '` = 1')->fetch(PDO::FETCH_ASSOC);
        if (!is_null($data[$this->rightBound]))
        {
            $leftB  = $right + 1;
            $rightB = $right + 2;
            $level  = $data[$this->level] + 1;
        }
        else
        {
            $leftB   = 1;
            $rightB  = 2;
            $level   = 0;
        }
        $prepare = $this->oSql->prepare('INSERT INTO ' . $this->tableName . ' (`' . $this->label . '`,`' . $this->leftBound . '`,`' . $this->rightBound . '`,`' . $this->level . '`) 
							VALUES(:nom,:left,:right,:level)');
        $prepare->execute(array(':nom' => $nom, ':left' => $leftB, ':right' => $rightB, ':level' => $level));

        return $this->oSql->last_id();
    }

    /**
     * Retourne un tableau de l'arborescence avec des niveaux de profondeur
     * 
     * @access public
     * @return array
     */
    public function getTree()
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $data   = $this->oSql->query('SELECT node.' . $this->label . ', (COUNT(parent.' . $this->label . ')-1) as depth 
							FROM ' . $this->tableName . ' as node,' . $this->tableName . ' as parent 
							WHERE node.' . $this->leftBound . ' BETWEEN parent.' . $this->leftBound . ' AND parent.' . $this->rightBound . ' 
							GROUP BY node.' . $this->label . '
							ORDER BY node.' . $this->leftBound . ',depth');
        while ($result = $data->fetch(PDO::FETCH_ASSOC))
            $tree[] = $result;

        return $tree;
    }

    /**
     * Retourne l'arborescence sous forme de balise HTML
     *
     * @access public
     * @return string
     */
    public function getHTMLTree()
    {
        $tree     = $this->getTree();
        //var_dump($tree);
        $depth    = 0;
        $htmlTree = '';
        $htmlTree .='<ul>';
        foreach ($tree as $value) {
            if ($depth < $value['depth'])
            {
                $htmlTree .=str_repeat('<ul>', ($value['depth'] - $depth));
                $depth = $value['depth'];
                $htmlTree .= '<li>' . $value[$this->label] . '</li>';
            }
            elseif ($depth > $value['depth'])
            {
                $htmlTree .= str_repeat('</ul>', ++$depth - $value['depth']);
                $depth = $value['depth'];
                $htmlTree .='<ul><li>' . $value[$this->label] . '</li>';
            }
            else
                $htmlTree .='<li>' . $value[$this->label] . '</li>';
        }
        $htmlTree.='</ul>';
        return $htmlTree;
    }

    /**
     * Compte les enfants de l'élément
     * 
     * @param int $option Permet de choisir si on veut uniquement les noeuds ou les feuilles
     * @access public
     * @return int
     */
    public function countChilds($option = null)
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $this->checkCurrent();

        if (!empty($option))
        {
            if (self::LEAFONLY == $option)
                $option = ' AND (`' . $this->rightBound . '`-`' . $this->leftBound . '`)=1';
            elseif (self::NODEONLY == $option)
                $option = ' AND (`' . $this->rightBound . '`-`' . $this->leftBound . '`)>1';
            else
                $option = '';
        }
        else
            $option = '';

        $child = $this->oSql->query('SELECT COUNT(*) 
									  FROM ' . $this->tableName . '
									  WHERE `' . $this->leftBound . '`>' . $this->current['left'] . ' 
									  	AND `' . $this->rightBound . '` < ' . $this->current['right'] . $option)
                ->fetchColumn(0);
        return $child;
    }

    /**
     * Retourne l'id et le label des enfants de l'éléments.
     * 
     * @param boolean $direct Liste que les fils de niveau n+1
     * @param int $option Permet de choisir si on veut uniquement les noeud ou les feuilles
     * @access public
     * @return array
     */
    public function getChilds($direct = false, $option = null)
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $this->checkCurrent();

        $childs = array();
        if (!empty($option))
        {
            if (self::LEAFONLY == $option)
                $option = ' AND (`' . $this->rightBound . '`-`' . $this->leftBound . '`)=1';
            elseif (self::NODEONLY == $option)
                $option = ' AND (`' . $this->rightBound . '`-`' . $this->leftBound . '`)>1';
            else
                $option = '';
        }
        else
            $option = '';

        if ($direct)
            $option .= ' AND ' . $this->level . ' = ' . ($this->current['level'] + 1);

        $child = $this->oSql->query('SELECT ' . $this->id . ',' . $this->label . ' 
									  FROM ' . $this->tableName . '
									  WHERE `' . $this->leftBound . '`>' . $this->current['left'] . ' 
									  	AND `' . $this->rightBound . '` < ' . $this->current['right'] . $option);

        while ($result   = $child->fetch(PDO::FETCH_ASSOC))
            $childs[] = $result;

        return $childs;
    }

    /**
     * Ajoute un enfant (par la droite) à l'élément
     *
     * @param string $nom Nom de l'élément à ajouter
     * @access public
     * @return id Id de l'élément ajouté
     */
    public function addChild($nom)
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $this->checkCurrent();

        // insertion après la borne droite du père
        $this->oSql->query('UPDATE ' . $this->tableName . ' 
							SET `' . $this->rightBound . '`=`' . $this->rightBound . '`+2
							WHERE `' . $this->rightBound . '`>=' . $this->current['right']);
        // 2 requetes de décalage pour éviter les problème d'unicité
        $this->oSql->query('UPDATE ' . $this->tableName . ' 
							SET `' . $this->leftBound . '`=`' . $this->leftBound . '`+2
							WHERE `' . $this->leftBound . '`>=' . $this->current['right']);


        $prepare = $this->oSql->prepare('INSERT INTO ' . $this->tableName . ' (`' . $this->label . '`,`' . $this->leftBound . '`,`' . $this->rightBound . '`,`' . $this->level . '`)
									VALUES(:nom,:left,:right,:level)');
        $prepare->execute(array(':nom' => $nom, ':left' => $this->current['right'], ':right' => $this->current['right'] + 1, ':level' => $this->current['level'] + 1));

        $this->current['right']+=2;
        return $this->oSql->last_id();
    }

    /**
     * Supprime l'élément courant si c'est une feuille
     *
     * @access public
     * @return boolean
     */
    public function delete()
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $this->checkCurrent();

        if (($this->current['right'] - $this->current['left'] == 1))
        {
            $this->oSql->query('DELETE FROM ' . $this->tableName . ' 
								WHERE `' . $this->leftBound . '` = ' . $this->current['left']);

            $this->oSql->query('UPDATE ' . $this->tableName . ' 
								SET `' . $this->leftBound . '`=`' . $this->leftBound . '`-2
								WHERE `' . $this->leftBound . '`>=' . $this->current['left']);

            $this->oSql->query('UPDATE ' . $this->tableName . ' 
								SET `' . $this->rightBound . '`=`' . $this->rightBound . '`-2
								WHERE `' . $this->rightBound . '`>=' . $this->current['left']);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Compte les parents de l'élément
     * @access public
     * @return int
     */
    public function countParents()
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $this->checkCurrent();

        $parent = $this->oSql->query('SELECT COUNT(*) 
									  FROM ' . $this->tableName . '
									  WHERE `' . $this->leftBound . '`<' . $this->current['left'] . ' 
									  	AND `' . $this->rightBound . '` > ' . $this->current['right'])
                ->fetchColumn(0);
        return $parent;
    }

    /**
     * Retourne les parents de l'élément
     *
     * @access public
     * @return array
     */
    public function getParents()
    {
        if (empty($this->oSql))
            throw new \RuntimeException("No db object set. Please use setDB() first");

        $this->checkCurrent();

        $parents = array();
        $parent = $this->oSql->query('SELECT ' . $this->id . ',' . $this->label . ' 
									  FROM ' . $this->tableName . '
									  WHERE `' . $this->leftBound . '`<' . $this->current['left'] . ' 
									  	AND `' . $this->rightBound . '` > ' . $this->current['right']);

        while ($result    = $parent->fetch(PDO::FETCH_ASSOC))
            $parents[] = $result;

        return $parents;
    }

    /**
     * Vérifie si un élément est séléctionné
     * @access private
     */
    private function checkCurrent()
    {
        if (empty($this->current))
        {
            throw new \Exception('Aucun élément courant n\'est défini');
        }
    }

}