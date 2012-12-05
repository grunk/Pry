$('document').ready(function(){
	$.get('pages/menu.html',function(data){
		$('#menuContainer').html(data);
		$('ul.nav li').on('click',function(e){
			e.preventDefault();
			$('ul.nav li').removeClass('active');
			$(this).addClass('active');
			var url = null;
			url = $(this).find('a').attr('href');
			if(url != undefined) {
				$.get(url,function(dataLoaded){
					$('#content').html(dataLoaded);
					SyntaxHighlighter.highlight();
				});
			}
		});
	});
});