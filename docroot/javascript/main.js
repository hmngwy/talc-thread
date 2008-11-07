window.addEvent('domready', function() {
	
	var INSERTINTERVAL = 400;
	
	var USERNAMEDEFAULT = 'yourname';
	var COMMENTDEFAULT = 'enter your message here';
	
	var COMMENTSEPARATOR = '<div class="sep2">&#8594;</div>';
	var INPUTSEPARATOR = {html:'&#8594;', 'style':'top:-3px;margin-left:6px;'};
	var LOADERSEPARATOR = {html:'<img src="/images/loader.gif" />', 'style':'top:0px;margin-left:8px;'};
	
	var TEXTCOLOR = '#333333';
	var FADEDTEXTCOLOR = '#CCCCCC';
	var DISABLEDTEXTCOLOR = '#999999';
	
	var INITRESOURCE = '/ajax/init.php';
	var UPDATERESOURCE = '/ajax/update.php';
	var WRITERESOURCE = '/ajax/write.php';
	
	var username_input = $('u');
	var comment_input = $('t');
	var topic_text = $('topic-text');
	var comments_list = $('comments-list');
	var pagebody = $('home');
	
	var is_thread_locked = false;
	
	var hidden_comments = [];
	
	var poll;
	var insert;
	
	var long_poller;
	
	//PRELOAD IMAGES
	new Request({method: 'get', url: '/images/loader.gif'}).get();
	
	//PRESETS
	set_topic('<span style="color:'+FADEDTEXTCOLOR+'">Loading...</span>');
	enable_inputs();
	
	window.addEvent('load', notice_remask);
	window.addEvent('resize', notice_remask);
	
	//SET INITIAL COMMENT VALUE
	comment_input.set('value', COMMENTDEFAULT);
	comment_input.set('style', 'color:'+FADEDTEXTCOLOR+';');
	
	username_input.set({
		'events': {
			'blur':function() 
			{
				if(this.get('value') == USERNAMEDEFAULT || this.get('value') == '')
				{
					this.set('style', 'color:'+FADEDTEXTCOLOR+';');
					this.set('value', USERNAMEDEFAULT);
				}
				else
				{
					this.set('style', 'color:'+TEXTCOLOR+';');
				}
			},
			
			'focus':function() 
			{
				if(this.get('value') == USERNAMEDEFAULT)
				{
					this.set('value', '');
					this.set('style', 'color:'+TEXTCOLOR+';');
				}
			},
			
			'keydown':function(event)
			{
				if (event.key == 'enter')
				{
					event.stop();
					comment_input.focus();
				}
			}
		}	
	});
	
	comment_input.set({
		'events' : {
			'blur':function()
			{
				if(this.get('value') == COMMENTDEFAULT || this.get('value') == '')
				{
					this.set('style', 'color:'+FADEDTEXTCOLOR+';');
					this.set('value', COMMENTDEFAULT);
				}
				else
				{
					this.set('style', 'color:'+TEXTCOLOR+';');
				}
			},
			
			'focus':function() 
			{
				if(this.get('value') == COMMENTDEFAULT)
				{
					this.set('value', '');
					this.set('style', 'color:'+TEXTCOLOR+';');
				}
			},
			
			'keydown':function(event)
			{
				if (event.key == 'enter')
				{
					event.stop();
					submit_message();
				}
			}
		}
	});
	
	//INITIALIZE THE PAGE
	var jsonRequest = new Request.JSON({url: INITRESOURCE, onComplete: function(response){
    	set_site_status(response.site);
    	set_topic(response.topic);
    	
    	if(response.username != null)
    	{
	    	username_input.set('value', response.username);
    	}
    	else
    	{
	    	username_input.set('value', USERNAMEDEFAULT);
			username_input.set('style', 'color:'+FADEDTEXTCOLOR+';');
    	}
    	
		if(response.comments != null)
		{
			insert_comments(response.comments);
		}
		else
		{
			start_polling();
		}
		
	}}).get();
	
	function insert_comments(comments)
	{
		$each(comments, function(comment){
			var text = comment.name+COMMENTSEPARATOR+comment.text;
			
			var e = new Element('div', {html:text, 'class':'comment'});
			e.inject(comments_list, 'top').slide('hide');
			
			hidden_comments.push(e);
		});
		
		hidden_comments = hidden_comments.reverse();
		
		function slide_in_comment()
		{
			var e = hidden_comments.pop();
			if(e!=null)
			{ 
				//THERE ARE STILL ITEMS TO BE INSERTED
				var s = new Fx.Slide(e, {duration:INSERTINTERVAL});
				s.slideIn();
			}
			else
			{
				//ALL ITEMS HAVE BEEN INSERTED
				$clear(insert); //END THE INSERT INTERVAL
				start_polling(); //CONTINUE FETCHING UPDATES
			}
		}
		
		insert = slide_in_comment.periodical(INSERTINTERVAL);
	}
	
	function update()
	{
		//MAKING SURE THERE IS ONLY ONE PERIODIC REQUEST RUNNING
		$clear(poll);
		
		long_poller = new Request.JSON({url: UPDATERESOURCE, onComplete: function(response){
			
			//UPDATE SITE STATUS
			set_site_status(response.site);
			
			if(response.topic != null)
			{
				set_topic(response.topic);
			}
			
			if(response.comments != null)
			{
				//THERE ARE ITEMS TO BE INSERTED
				insert_comments(response.comments); //START INSERTING
				$clear(poll); //POSTPONE FETCHING OF UPDATES
			}
			
			start_polling();
					
		}}).get();
	}
	
	function submit_message()
	{
		if(username_input.value!='' && comment_input.value!=''
			&& username_input.value!=USERNAMEDEFAULT && comment_input.value!=COMMENTDEFAULT)
		{
			$clear(poll);
			long_poller.cancel();
			
			disable_inputs();
			
			$('sep').set(LOADERSEPARATOR);
						
			var jsonRequest = new Request.JSON({url: WRITERESOURCE, onComplete: function(response){
				
				if(response.status=='success')
				{
					//WE KNOW THAT THERE IS A NEW COMMENT, AND IT WILL BE SURE THAT function insert_comments() WILL BE CALLED
					//THAT IN TURN WILL CONTINUE THE POLL
					update();
					comment_input.value = '';
				}
				else if(response.status=='fail')
				{
					//CONTINUE THE POLL
					start_polling();
				}
				
				enable_inputs();
				
				username_input.focus(); //INVISIBLE CURSOR HACK
				comment_input.focus();
				
				$('sep').set(INPUTSEPARATOR);
				
			}}).post({username:username_input.value, comment:comment_input.value});
		}
	}
	
	function disable_inputs()
	{
		username_input.disabled = true;
		comment_input.disabled = true;
		username_input.set({'style':'color:'+DISABLEDTEXTCOLOR+';'});
		comment_input.set({'style':'color:'+DISABLEDTEXTCOLOR+';'});
	}
	
	function enable_inputs()
	{
		username_input.disabled = false;
		comment_input.disabled = false;
		username_input.set({'style':'color:'+TEXTCOLOR+';'});
		comment_input.set({'style':'color:'+TEXTCOLOR+';'});
	}
	
	
	function notice_remask()
	{
		if($('notice'))
		{
			$('notice').setStyle('height', pagebody.getScrollHeight());	
			
		}
	}
	
	function start_polling()
	{
		$clear(poll);
		poll = update.periodical(1);
	}
	
	
	//
	function set_topic(text)
	{
		topic_text.set('html', text);
	}
	
	//
	function set_notice(image, text)
	{
		notice_mask_e = new Element('div', {id:'notice'});
		notice_poster_e  = new Element('div', {id:'notice-poster'});
		notice_image_e  = new Element('img', {src:image});
		notice_text_e  = new Element('div', {html:text, id:'notice-text'});
		
		notice_image_e.inject(notice_poster_e);
		notice_text_e.inject(notice_poster_e);
		notice_poster_e.inject(notice_mask_e);
		notice_mask_e.inject(pagebody, 'top');
		
		is_thread_locked = true;
		
		disable_inputs
		
		notice_mask_e.setStyle('height', pagebody.getScrollHeight());
	}
	
	//
	function unset_notice()
	{
		$('notice').dispose();
		is_thread_locked = false;
		
		enable_inputs();
		
		username_input.focus();
		comment_input.focus();
	}
	
	function set_site_status(site)
	{
		switch(site.status)
		{
			case 'locked':
				if(is_thread_locked == false)
				set_notice('/images/thread-locked.png', site.notice);
				break;
				
			case 'maintenance':
				if(is_thread_locked == false)
				set_notice('/images/site-maintenance.png', site.notice);
				break;
				
			case 'ready': 
				if(is_thread_locked == true)
				unset_notice();
				break;
		}	   
	}
	
});