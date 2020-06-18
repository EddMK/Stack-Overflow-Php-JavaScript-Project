<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Stuck Overflow</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="https://kit.fontawesome.com/9f16cf7640.js" crossorigin="anonymous"></script>
		<script type="text/javascript" src="lib/jquery-3.5.1.min.js"></script>
		<script>
			let page,menu,search;
			page = 1;
			menu = "newest";
			
			$(function(){
				$(".questions").empty();
				$(".pagination").empty();
				$("#buttonsearch").hide();
				$("ul.ul_menu_questions li a").css( "color", "black" );
				$("#newest").css( "color", "green" );
				$.post("post/newest",{page : page}, function(donnees){
					$.each(JSON.parse(donnees),function(key,value){
						var retour_li="<li>";
						retour_li +="<p><a href='post/show/"+value.postid+"'  >"+value.titre+" </a> </p>";
						retour_li +="<p>"+value.body+"</p>";
						retour_li += "<p><b>Asked "+value.ago+" by "+value.fullname+"("+value.score+" vote(s),"+value.answers+" answer(s))</b></p>";						
						$.each(value.tags,function(key,value){
							//console.log(typeof(value.tagname));
							retour_li +="<a href='#' onclick='display_tag(event,\""+value.tagname+"\", "+value.tagid+");' >"+value.tagname  +"  </a>";
						});
						retour_li +="</li>";
						$(".questions").append(retour_li);
					});	
				});
				$.get("post/number", function(donnees){
					numberquestions = donnees;
					//console.log(numberquestions);
					totalPages = numberquestions  / 5;
					totalPages = Math.ceil(totalPages);
					
					for(let i = 1; i<= totalPages; i++){
						$(".pagination").append("<a href='#'  id='page"+i+"'  onclick='change_page(event,"+i+");'>"+i+"</a>");
					}
					$("#page"+page).css("color", "orange");
				});
				
				$("#searchpost").on("change keyup paste", function(){
					event.preventDefault();
					menu = "search";
					$(".questions").empty();
					$(".pagination").empty();
					if($("#tagged").length){
						$("#tagged").remove();
					}
					if(!$("#searchresults").length){
						searchadded = "<li><a id='searchresults'  href='#'  onclick='change_menu(event,'search');'  >Search Results</a></li>";
						$(".ul_menu_questions").append(searchadded);
						$("ul.ul_menu_questions li a").css( "color", "black" );
						$("#searchresults").css( "color", "green" );
					}
					search = $("#searchpost").val();
					$.post("post/search",{page : page, search : search}, function(donnees){
						//console.log(donnees);
						$.each(JSON.parse(donnees),function(key,value){
							var retour_li="<li>";
							retour_li +="<p><a href='post/show/"+value.postid+"'  >"+value.titre+" </a> </p>";
							retour_li +="<p>"+value.body+"</p>";
							retour_li += "<p><b>Asked "+value.ago+" by "+value.fullname+"("+value.score+" vote(s),"+value.answers+" answer(s))</b></p>";						
							$.each(value.tags,function(key,value){
								retour_li +="<a href='#' onclick='display_tag(event,\""+value.tagname+"\", "+value.tagid+");' >"+value.tagname  +"  </a>";
							});
							retour_li +="</li>";
							$(".questions").append(retour_li);
						});	
					});
					$.post("post/numbersearchs",{search : search} ,function(donnees){
						numberquestions = donnees;
						//alert(numberquestions);
						totalPages = numberquestions  / 5;
						totalPages = Math.ceil(totalPages);
						for(let i = 1; i<= totalPages; i++){
							$(".pagination").append("<a href='#'  id='page"+i+"'  onclick='change_page(event,"+i+");'>"+i+"</a>");
						}
						$("#page"+page).css("color", "orange");
					});					
				})

				
			});
			
			function recup_data(type){
				$(".questions").empty();
				if(type==="search"){
					$.post("post/search",{page : page, search : search}, function(donnees){
						//console.log(donnees);
						$.each(JSON.parse(donnees),function(key,value){
							var retour_li="<li>";
							retour_li +="<p><a href='post/show/"+value.postid+"'  >"+value.titre+" </a> </p>";
							retour_li +="<p>"+value.body+"</p>";
							retour_li += "<p><b>Asked "+value.ago+" by "+value.fullname+"("+value.score+" vote(s),"+value.answers+" answer(s))</b></p>";						
							$.each(value.tags,function(key,value){
								retour_li +="<a href='#' onclick='display_tag(event,\""+value.tagname+"\", "+value.tagid+");' >"+value.tagname  +"  </a>";
							});
							retour_li +="</li>";
							$(".questions").append(retour_li);
						});	
					});
				}else if(typeof(type) === "number"){
					console.log(page);
					$.post("post/tag",{page : page, type : type}, function(donnees){
						
						//console.log(donnees);
						$.each(JSON.parse(donnees),function(key,value){
							var retour_li="<li>";
							retour_li +="<p><a href='post/show/"+value.postid+"'  >"+value.titre+" </a> </p>";
							retour_li +="<p>"+value.body+"</p>";
							retour_li += "<p><b>Asked "+value.ago+" by "+value.fullname+"("+value.score+" vote(s),"+value.answers+" answer(s))</b></p>";						
							$.each(value.tags,function(key,value){
								retour_li +="<a href='#' onclick='display_tag(event,\""+value.tagname+"\", "+value.tagid+");' >"+value.tagname  +"  </a>";
							});
							retour_li +="</li>";
							$(".questions").append(retour_li);
						});	
					});
				}else{
					let path = "post/"+type;				
					$.post(path,{page : page}, function(donnees){
						$.each(JSON.parse(donnees),function(key,value){
							var retour_li="<li>";
							retour_li +="<p><a href='post/show/"+value.postid+"'  >"+value.titre+" </a> </p>";
							retour_li +="<p>"+value.body+"</p>";
							retour_li += "<p><b>Asked "+value.ago+" by "+value.fullname+"("+value.score+" vote(s),"+value.answers+" answer(s))</b></p>";						
							$.each(value.tags,function(key,value){
								retour_li +="<a href='#' onclick='display_tag(event,\""+value.tagname+"\", "+value.tagid+");' >"+value.tagname  +"  </a>";
								//console.log(value);
							});
							retour_li +="</li>";
							$(".questions").append(retour_li);
						});	
					});
				}
			}
			
			function change_menu(e,newmenu){
				e.preventDefault();
				menu = newmenu;
				page = 1;
				recup_data(menu);
				if($("#searchresults").length){
					$("#searchresults").remove();
					$("#searchpost").val('');
					//il manque la pagination
					$(".pagination").empty();
					$.get("post/number", function(donnees){
						numberquestions = donnees;
						//console.log(numberquestions);
						totalPages = numberquestions  / 5;
						totalPages = Math.ceil(totalPages);
						
						for(let i = 1; i<= totalPages; i++){
							$(".pagination").append("<a href='#'  id='page"+i+"'  onclick='change_page(event,"+i+");'>"+i+"</a>");
						}
						$("#page"+page).css("color", "orange");
					});
				}
				if($("#tagged").length){
					$("#tagged").remove();
					//il manque la pagination
					$(".pagination").empty();
					$.get("post/number", function(donnees){
						numberquestions = donnees;
						//console.log(numberquestions);
						totalPages = numberquestions  / 5;
						totalPages = Math.ceil(totalPages);
						
						for(let i = 1; i<= totalPages; i++){
							$(".pagination").append("<a href='#'  id='page"+i+"'  onclick='change_page(event,"+i+");'>"+i+"</a>");
						}
						$("#page"+page).css("color", "orange");
					});
				}
				$("ul.ul_menu_questions li a").css( "color", "black" );
				$("#"+menu).css( "color", "green" );	
				$(".pagination a").css("color", "black");
				$("#page"+page).css("color", "orange");
			}
			
			function change_page(e,newpage){
				e.preventDefault();
				page = newpage;
				recup_data(menu);
				$(".pagination a").css("color", "black");
				$("#page"+page).css("color", "orange");
			}
			
			
			function display_tag(e,name,id){
				e.preventDefault();
				page = 1;
				menu = id;
				tagid = menu;
				$(".pagination").empty();			
				if($("#tagged").length){
					$("#tagged").remove();
				}
				if($("#searchresults").length){
					$("#searchresults").remove();
					$("#searchpost").val('');
				}
				tagged = "<li><a href='#'  id='tagged'> Questions tagged["+name+"] </a></li>";
				$(".ul_menu_questions").append(tagged);
				$("ul.ul_menu_questions li a").css( "color", "black" );
				$("#tagged").css( "color", "green" );
				console.log(id);
				recup_data(id);
				$.post("post/numbertags",{tagid : tagid} ,function(donnees){
					console.log(donnees);
						numberquestions = donnees;
						totalPages = numberquestions  / 5;
						totalPages = Math.ceil(totalPages);
						for(let i = 1; i<= totalPages; i++){
							$(".pagination").append("<a href='#'  id='page"+i+"'  onclick='change_page(event,"+i+");'>"+i+"</a>");
						}
						$("#page"+page).css("color", "orange");
				});
				
			}
		</script>
    </head>
    <body>
		<?php include('menu.html'); ?>
		<div class="main">
			<div class="menu" >
				<div class="sort_questions">
					 <ul class="ul_menu_questions">
						  <li><a id="newest" class="<?php if($menu == "newest"){ echo "current" ; }  ?>" href="post/index/newest"   onclick="change_menu(event,'newest');">Newest</a></li>
						  <li><a id="votes" class="<?php  if($menu == "votes"){ echo "current"; } ?>"  href="post/index/votes" onclick="change_menu(event,'votes');">Votes</a></li>
						  <li><a id="unanswered" class="<?php  if($menu == "unanswered"){ echo "current"; }  ?>"  href="post/index/unanswered"  onclick="change_menu(event,'unanswered');" >Unanswered</a></li>
						  <li><a id="active" class="<?php  if($menu == "active"){ echo "current"; }  ?>"  href="post/index/active"  onclick="change_menu(event,'active');"  >Active</a></li>

						  <?php if($menu == "tag"){ ?>
							<li><a class='current'>Questions tagged[<?= $tag->tagName ?>]</li>
						  <?php }?>
					</ul> 
				</div>
				<div class="search">
					<form action="post/index" method="post" >
						<input type="text" id="searchpost" name="search" value="<?= $search ?>"  placeholder="Search...">
						<button id="buttonsearch" type="submit">submit</button> 
					</form>
				</div>
			</div>
			<div class="Questions">
				<ul class ="questions" >
					<?php foreach ($posts as $post){ ?>	
							<li>
								<p><a href="post/show/<?= $post->get_postid()?>"  > <?= $post->title ?> </a> </p>
								<p>
									<?php 
										$Parsedown = new Parsedown();
										echo $Parsedown->text($post->body);
									?>
								</p>
								<p><b>
									Asked <?= $post->get_ago()?> by <?= $post->get_author_by_authorId()->fullName ?>
									(<?= $post->get_score()?> vote(s),<?= $post->number_of_answers() ?> answer(s))
									<?php foreach ($post->get_tags() as $tag){ ?>
										<a href="post/posts/tag/1/<?= $tag->get_tagId()?>" ><?= $tag->tagName?></a>
									<?php } ?>
								</b></p>
							</li>
					<?php } ?>
				</ul>
			</div>
			<div class="pagination">
				  <?php for($i = 1; $i<=$totalPages;$i ++){ ?>
					  <?php if($menu == "search"){?>
						<?php $_POST['search']=$search ?>
						<a href="post/index/<?= $menu ?>/<?= $i ?>/<?= $search ?>"    class="<?php if($currentPage == $i){ echo "current" ; }  ?>"       ><?= $i ?></a>
					  <?php }elseif($menu == "tag"){?>
						<a href="post/posts/tag/<?= $i ?>/<?= $tag->get_tagId()?>"    class="<?php if($currentPage == $i){ echo "current" ; }  ?>"       ><?= $i ?> </a>
					  <?php }else{ ?>
						<a href="post/index/<?= $menu ?>/<?= $i ?>"    class="<?php if($currentPage == $i){ echo "current" ; }  ?>"    ><?= $i ?></a>
					  <?php } ?>
				  <?php } ?>
			</div>
		</div>
		</div>
    </body>
</html>