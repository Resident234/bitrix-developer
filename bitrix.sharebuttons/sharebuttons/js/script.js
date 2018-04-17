	Share = {
		vkontakte: function(purl, ptitle, pimg, text) {
			url  = 'http://vkontakte.ru/share.php?';
			url += 'url='          + encodeURIComponent(purl);
			url += '&title='       + encodeURIComponent(ptitle);
			url += '&description=' + encodeURIComponent(text);
			url += '&image='       + encodeURIComponent(pimg);
			url += '&noparse=true';
			Share.popup(url);
		},
		facebook: function(purl, ptitle, pimg, text) {

			var currentLang = $("input[data-current-lang]").attr("data-current-lang");
			
			url  = 'http://www.facebook.com/sharer.php?s=100';
			//url  = 'http://www.facebook.com/sharer/sharer.php?s=100';
			url += '&p[title]='     + encodeURIComponent(ptitle);
			url += '&p[summary]='   + encodeURIComponent(text);
			url += '&p[url]='       + encodeURIComponent(purl+"&lang="+currentLang);
			url += '&p[images][0]=' + encodeURIComponent(pimg);


				Share.popup(url);
		},
		twitter: function(purl, ptitle) {
			//url  = 'http://twitter.com/share?';
			url  = 'http://twitter.com/intent/tweet?';
			url += 'text='      + encodeURIComponent(ptitle);
			url += '&url='      + encodeURIComponent(purl);
			//url += '&counturl=' + encodeURIComponent(purl);
			//url = "https://twitter.com/intent/tweet?text=%D0%9F%D0%BE%D0%B4%D0%B2%D0%B5%D1%81%D0%BA%D0%B0%201206%20%281%29&url=http%3A%2F%2Fannabronze.amado.su%2Fcatalog%2Fzima%2Fpodveska-1206-1%2F%3Foffer%3D78508";

			Share.popup(url);
		},
		googleplus: function(purl) {
			url  = 'https://plus.google.com/share?';
			url += 'url=' + encodeURIComponent(purl);
			Share.popup(url)
		},
		odnoklassniki: function(purl, text) {
			url  = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1';
			url += '&st.comments=' + encodeURIComponent(text);
			url += '&st._surl='    + encodeURIComponent(purl);
			Share.popup(url);
		},
		mailru: function(purl, ptitle, pimg, text) {
			url  = 'http://connect.mail.ru/share?';
			url += 'url='          + encodeURIComponent(purl);
			url += '&title='       + encodeURIComponent(ptitle);
			url += '&description=' + encodeURIComponent(text);
			url += '&imageurl='    + encodeURIComponent(pimg);
			Share.popup(url)
		},
		livejournal: function(purl, ptitle, pimg, text) {
			url  = 'http://www.livejournal.com/update.bml?';
			url += 'event='          + encodeURIComponent(purl);
			url += '&subject='       + encodeURIComponent(ptitle);
			Share.popup(url)
		},
		pinterest: function(purl, ptitle, pimg, text) {
			//url  = 'http://www.livejournal.com/update.bml?';
			//url += 'event='          + encodeURIComponent(purl);
			//url += '&subject='       + encodeURIComponent(ptitle);
			url = "https://assets.pinterest.com/ext/grid.html?1481278509876?1481278510626";
			Share.popup(url)
		},


		popup: function(url) {
			window.open(url,'','toolbar=0,status=0,width=626,height=436');
		}
	};

