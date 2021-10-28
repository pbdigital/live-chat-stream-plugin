$ = jQuery;
let timer = false;
let liveStreamPageId = "";

let liveStreamGetComments = () => {

	//if(liveStreamPageId=="") return false;

	var lastId = $('#live_event_comment_last_id').val();
	$.ajax({
		url: ajaxurl,
		dataType: 'json',
		data : {
			action : 'pbd_event_stream',
			last_id : lastId,
			post_id : liveStreamPageId
		},
		success : function(response){
			$('.live_event_comments .loading').hide();
			var tpl = '';
			if (response.comments !== null) { 
				
				$.each(response.comments, function(k, v) {
					tpl += `
						<div class="comment">
							<div class="avatar">
								<img src="${v.avatar}">
							</div>
							<div class="stream_comment_section">
								<div class="date">
									${v.comment_date}
								</div>
								<div class="stream_comment">
									<span>${v.name}: </span> 
									${v.comment_content}
								</div>
							</div>
						</div>
					`;
				});
				$('.live_event_comments').prepend(tpl);
				$('#live_event_comment_last_id').val(response.last_id);
				if(timer == false)
				{
					liveStreamStartTimer();
				}
			}
		}
	});
}

function liveStreamStartTimer(){
	timer = setInterval(liveStreamGetComments, 10000);
}	
function liveStreamClearTimer(){
	clearInterval(timer);
	timer = false;
}
$(document).on("keyup", '#live_event_comments_message', '#live_event_comments_message', function(){
	if ($(this).val() == "" )
	{
		$('#live_event_comments_btn').prop('disabled', true);
	} else {
		$('#live_event_comments_btn').prop('disabled', false);
	}
	
});
$(document).on("click", '#live_event_comments_btn', function(){
if(liveStreamPageId=="") return false;
	var live_event_comments_message = $('#live_event_comments_message').val();
	
	//Can't be empty
	if (live_event_comments_message == ""){
		return false;
	}
	//Clear the background refresh timer. It will be started again in getComments()
	liveStreamClearTimer();
	$('#live_event_comments_btn').prop("disabled", true);
	$('#live_event_comments_btn svg:not(.spinner)').hide();
	$('#live_event_comments_btn svg.spinner').show();
	
	$.ajax({
		url: ajaxurl,
		dataType: 'json',
		method: 'POST',
		data : {
			action : 'pbd_event_stream_add_comment',
			post_id : liveStreamPageId,
			message: live_event_comments_message
		},
		success : function(response){
			if (response.success) { 
				liveStreamGetComments();
				$('#live_event_comments_message').val('');
				$('#live_event_comments_btn').prop('disabled', true);
				$('#live_event_comments_btn svg:not(.spinner)').show();
				$('#live_event_comments_btn svg.spinner').hide();
			}
		}
	});
});

