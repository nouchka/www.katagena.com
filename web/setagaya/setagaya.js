$(function() {
	$.urlParam = function(name){
		var results = new RegExp('[\\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
		if(!results){
			return 0;
		}
		return results[1] || 0;
	}
	var template = Hogan.compile(
	            '<li class="{{css_class}}" id="li-{{task_id}}">' +
	                    '<div>' +
		                    '<span class="title">{{task_name}}</span>' +
		                    '<span class="time right">{{task_time}}h</span>' +
		                '</div>' +
	                    '<div>' +
	                        '<small class="category">{{task_category}}</small>' +
	                    '</div>' +
	                    '<button data-dismiss="alert" class="close" type="button" title="finish task">x</button>' +
	                '</li>'
	        );
	var templateUser = Hogan.compile(
		'{{#users}}' +
		'<div class="section span{{span_number}}" id="today-{{id}}">' +
			'<h2>{{name}} (<span class="count">0</span>h)</h2>' +
			'<ul id="sortable-{{name}}" class="connectedSortable">' +
			'</ul>' +
		'</div>' +
		'{{/users}}'
	);
	var templateUserRight = Hogan.compile(
		'{{#users}}' +
		'<div class="btn-group">' +
			'<button data-toggle="dropdown" class="btn dropdown-toggle">{{name}} <span class="caret"></span></button>' +
			'<ul class="dropdown-menu">' +
			//'<li><a href="./form.php?right=act&lvl=1&user={{id}}&project={{project}}">Is part of planning</a></li>' +
			'{{^owner}}'+
			'<li class="divider"></li>' +
			'<li><a href="./save.php?action=remove-user&u_id={{id}}&p_id={{project}}">Remove</a></li>' +
			'{{/owner}}'+
			'</ul>' +
		'</div><!-- /btn-group -->' +
		'{{/users}}'
	);
	var p_id = 1;
	if($.urlParam('p_id') && parseInt($.urlParam('p_id'))> 0){
		p_id = parseInt($.urlParam('p_id'));
	}
	$.ajax({
		type: 'GET',
		url: './data?p_id='+p_id,
		dataType: 'json',
		success: function(jsonData) {
			jQuery('#add-user-pid').val(p_id);
			jsonData.span_number = Math.floor(12/jsonData.users.length);
			jQuery('#users').html(templateUser.render(jsonData));
			jQuery('#user-rights').html(templateUserRight.render(jsonData));
			if(p_id != 1 && jsonData.socket_io == "true"){
				try{
					var socket = io.connect('http://katagena.com/');
					socket.on('date-<?php echo $p_id;?>', function(data){
						$.getJSON('./get.php?t_id='+data.date, function(data) {
							$('.title', '#li-'+data.task.task_id).text(data.task.task_name);
							$('.time', '#li-'+data.task.task_id).text(data.task.task_time+"h");
							$('.category', '#li-'+data.task.task_id).text(data.task.task_category);
							$('#li-'+data.task.task_id).data('time', data.task.task_time);
							$('#li-'+data.task.task_id).data('order', data.task.task_order);
							var levels = new Array();
							levels[0] = "nuni";
							levels[1] = "uni";
							levels[2] = "nui";
							levels[3] = "ui";
							if(data.task.task_lvl != ''){
								$('#li-'+data.task.task_id).data('level', levels[data.task.task_lvl]);
							}
							if(data.task.task_owner>0){
								$('#li-'+data.task.task_id).data('level', "today-"+data.task.task_owner);
							}
							updateTask(data.task.task_id);
						});
					});
				}catch(err){
					console.log('auto-refresh not enabled');
				}
			}
			if (typeof jsonData.tasks !== 'undefined') {
				$.each(jsonData.tasks, function(i, item) {
					$('#t_id').append($('<option>', { 
						value: item.task_id,
						text : item.task_name 
					}));
					if(item.task_lvl == "0"){
						item.css_class="alert alert-success";
					}else if(item.task_lvl == "1"){
						item.css_class="alert alert-info";
					}else if(item.task_lvl == "2"){
						item.css_class="alert";
					}else if(item.task_lvl == "3"){
						item.css_class="alert alert-error";
					}
					var render = template.render(item);
					var name
					if(item.task_owner > 0){
						jQuery('#today-'+item.task_owner+' .connectedSortable').append(render);
					}else{
						jQuery('#sortable-'+item.task_lvl).append(render);
					}
					$( "#li-"+item.task_id+" button.close" ).click(function() {
						var url = "./save.php";
						$.ajax({
							url: url,
							data: { action: "task-done", t_id: $(this).parent("li").attr('id'), p_id: p_id },
							success: function(data) {
	
	
	
	
	                        	  $( "ul.connectedSortable" ).each(function( index ) {
	          				        var time=0;
	          				        $( "li", $(this)).each(function( index ) {
	          				        	time += parseInt($(this).data('time'));
	          				        	if($(this).data('order') != index && p_id != 1){
	          				                var url = "./save.php";
	          				                $.ajax({
	          				                    url: url,
	          				                    data: { action: "task-order", t_id: $(this).attr('id'), p_id: p_id, order: index },
	          				                    success: function(data) {
	          				                        $('#li-'+data.task.task_id).data('order', data.task.task_order);
	          				                    }
	          				                });
	          				        	}
	          				        });
	          				        $('.count', $(this).parent()).text(time);
	          				    });
	
	
	
	
							}
						});
					});
					jQuery('#li-'+item.task_id).data('time', item.task_time);
					jQuery('#li-'+item.task_id).data('order', item.task_order);
					
					
					
					
					
					$( ".connectedSortable" ).sortable({
						connectWith: ".connectedSortable",
						start: function(event, ui) {
							item = ui.item;
							newList = oldList = ui.item.parent().parent();
						},
						stop: function(event, ui) {
				          var url = "./save.php";
				          var id = getTaskId($(item));
				          $(item).data('level', newList.attr('id'));
				          updateTask(id);
				    	  if(p_id != 1){
				          $.ajax({
				          	  url: url,
				          	  data: { action: "planning-task", t_id: item.attr('id'), p_id: p_id, from: oldList.attr('id'), to: newList.attr('id') },
				          	  success: function(data) {}
				          	});
				    	  }
				          $( "ul.connectedSortable" ).each(function( index ) {
				              var time=0;
				              $( "li", $(this)).each(function( index ) {
				              	time += parseInt($(this).data('time'));
				              	if($(this).data('order') != index && p_id != 1){
				                      var url = "./save.php";
				                      $.ajax({
				                          url: url,
				                          data: { action: "task-order", t_id: $(this).attr('id'), p_id: p_id, order: index },
				                          success: function(data) {
				                              $('#li-'+data.task.task_id).data('order', data.task.task_order);
				                          }
				                      });
				              	}
				              });
				              $('.count', $(this).parent()).text(time);
				          });
				      },
				      change: function(event, ui) {
				          if(ui.sender) newList = ui.placeholder.parent().parent();
				      }
				      }).disableSelection();
					
					
					
	
				    $( "ul.connectedSortable" ).each(function( index ) {
				        var time=0;
				        $( "li", $(this)).each(function( index ) {
				        	time += parseInt($(this).data('time'));
				        	if($(this).data('order') != index && p_id != 1){
				                var url = "./save.php";
				                $.ajax({
				                    url: url,
				                    data: { action: "task-order", t_id: $(this).attr('id'), p_id: p_id, order: index },
				                    success: function(data) {
				                        $('#li-'+data.task.task_id).data('order', data.task.task_order);
				                    }
				                });
				        	}
				        });
				        $('.count', $(this).parent()).text(time);
				    });
					
					
	
			    	  
				    
				    
					
				});
			}
		},
		error: function() {
			alert('Error loading ');
		}
	});
	
    var oldList, newList, item;
    var updateTask = function(taskId) {
        var $div = $(getDivId(taskId));
        console.log($div);
        //Change css
        var level = $div.data("level");
        if(level == "ui" || level == "nui" || level == "uni" || level == "nuni"){
            $div.removeClass("alert-error");
            $div.removeClass("alert-info");
            $div.removeClass("alert-success");
        }
        if(level == "ui"){
            $div.addClass("alert-error");
        }else if(level == "uni"){
            $div.addClass("alert-info");
        }else if(level == "nuni"){
            $div.addClass("alert-success");
        }
        //change place
        var parent = $div.parents('.section').attr('id');
        if(parent != level){
            $div.appendTo('#'+level+' ul');
        }
    };
    var getTaskId = function(item) {
        return item.attr('id').substring(3);
    };
    var getDivId = function(id) {
        return "#li-"+id;
    };

  });