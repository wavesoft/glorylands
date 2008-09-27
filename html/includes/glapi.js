var isInternetExplorer=(navigator.userAgent.indexOf("MSIE")>=0);var isMozilla=(navigator.userAgent.indexOf("Gecko")>=0);var isOpera=(navigator.userAgent.indexOf("Opera")>=0);function $trace(A){ans="";$each(A,function(C,B){if(ans!=""){ans+=", "}ans+="["+B+"] = "+C});return ans}var CBChain=new Class({initialize:function(){this.chain=[]},register:function(B,A){if(!$defined(this.chain[B])){this.chain[B]=[]}this.chain[B].push(A)},call:function(B,A){if($defined(this.chain[B])){this.chain[B].each(function(C){C(A)})}}});var callback=new CBChain();var waiterShown=false;var waiterFX=false;var waiterDisposer=0;function initWaiter(){waiterFX=new Fx.Styles("waiter",{duration:400,transition:Fx.Transitions.Back.easeIn,onComplete:function(){if(!waiterShown){$("waiter_host").setStyles({display:"none"})}}});$("waiter_host").setStyles({display:"none"})}function showStatus(C,A){try{if(waiterDisposer>0){clearTimeout(waiterDisposer);waiterDisposer=0}if(!C){if(waiterShown){waiterShown=false;waiterFX.stop();waiterFX.start({opacity:0})}}else{$("waiter").setHTML(C);if(!waiterShown){$("waiter_host").setStyles({display:""});waiterShown=true;waiterFX.stop();waiterFX.start({opacity:1})}if(A){waiterDisposer=setTimeout(function(){showStatus()},A)}}}catch(B){}}var winCache=[];var lastZ=1000;function createWindow(E,G,L,I,M,F){if(!winCache[E]){var A=document.createElement("div");var D=document.createElement("div");var C=document.createElement("span");var K=document.createElement("a");var J=document.createElement("a");if(!isInternetExplorer){A.setAttribute("class","container");D.setAttribute("class","dragger");C.setAttribute("class","content");K.setAttribute("class","toggle");J.setAttribute("class","dispose")}else{A.setAttribute("className","container");D.setAttribute("className","dragger");C.setAttribute("className","content");K.setAttribute("className","toggle");J.setAttribute("className","dispose")}K.setAttribute("title","Minimize");J.setAttribute("title","Close");D.innerHTML='<span align="top" class="left">&nbsp;</span><span class="center">'+E+'</span><span class="right">&nbsp;</span>';A.appendChild(D);A.appendChild(C);C.innerHTML=G;if(!isMozilla){var B=new Fx.Slide(C)}K.setAttribute("href","javascript:void(null)");K.innerHTML="&nbsp;";D.appendChild(K);$(K).addEvent("click",function(N){N=new Event(N);if(!isMozilla){B.toggle()}else{if(C.style.display=="none"){C.style.display=""}else{C.style.display="none"}}N.stop()});J.setAttribute("href","javascript:void(null)");J.innerHTML="&nbsp;";D.appendChild(J);$(J).addEvent("click",function(N){N=new Event(N);A.remove();winCache[E]=false;N.stop()});$(D).addEvent("mousedown",function(N){A.setStyles({opacity:0.7,"z-index":lastZ++})});$(D).addEvent("mouseup",function(N){A.setStyles({opacity:1})});$(A).addEvent("mousedown",function(N){A.setStyles({"z-index":lastZ++})});if(L){A.setStyles({left:L})}if(I){A.setStyles({top:I})}if(M){A.setStyles({width:M})}if(F){A.setStyles({height:F})}A.setStyles({"z-index":lastZ++});document.body.appendChild(A);var H=new Drag.Move(A,{handle:D});winCache[E]=[A,C,B];return A}else{var A=winCache[E][0];var C=winCache[E][1];var B=winCache[E][2];if(!isMozilla){B.slideIn()}else{C.style.display=""}C.innerHTML=G;if(M){A.setStyles({width:M})}if(F){A.setStyles({height:F})}}}var data_cache=new Array();var ex_buffer_data="";function initDisplayBuffer(){ex_buffer_data=$("datapane").innerHTML}function displayBuffer(B,E,A,D,C){var F=B;if(E){data_cache.push($("datapane").innerHTML);F+='<span class="maplabel"><a class="navlink" href="javascript:restoreview();" title="Return to previous window"><img align="absmiddle" src="images/UI/navbtn_back.gif" /></a> Return</span>'}else{data_cache=new Array();if(C!=""){if(A!=""){if(D!=""){F+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+A+'\',false,true);"><img align="absmiddle" src="images/'+D+'" /></a> '+C+"</span>"}else{F+='<span class="maplabel"><a class="navlink" href="javascript:gloryIO(\''+A+"',false,true);\"> "+C+"</a></span>"}}else{if(D!=""){F+='<span class="maplabel"><img align="absmiddle" src="images/'+D+'" /> '+C+"</span>"}else{F+='<span class="maplabel"> '+C+"</span>"}}}}F+=ex_buffer_data;$("datapane").innerHTML=F}function restoreview(){if(data_cache.length<1){return }var A=data_cache.pop();$("datapane").innerHTML=A}var ddw_visible=false;function ddwin_dispose(){if(ddw_visible){var A=new Fx.Styles("dd_popup",{duration:500,transition:Fx.Transitions.Cubic.easeOut});var D=new Fx.Styles("dd_content",{duration:500,transition:Fx.Transitions.Cubic.easeOut});var B=$("dd_popup");var C=$("dd_host");var E=$("dd_content");D.start({opacity:0}).chain(function(){E.setHTML("");A.start({opacity:0,width:10,height:10}).chain(function(){C.setStyles({display:"none"})})});ddw_visible=false}}function ddwin_show(D,A,H){var C=$("dd_popup");var G=$("dd_content");var F=$("dd_host");var B=new Fx.Styles("dd_popup",{duration:500,transition:Fx.Transitions.Cubic.easeOut});var E=new Fx.Styles("dd_content",{duration:500,transition:Fx.Transitions.Cubic.easeOut});if(ddw_visible){E.start({opacity:0}).chain(function(){B.start({height:A,width:D,opacity:1}).chain(function(){G.setHTML('<div style="position:relative; width:100%; height:100%"><span class="dd_head"><a href="javascript:ddwin_dispose()">X</a></span>'+H+"</div>");E.start({opacity:1})})})}else{F.setStyles({display:""});C.setStyles({opacity:0,width:10,height:10});G.setStyles({opacity:0,display:"none"});G.setHTML('<div style="position:relative; width:100%; height:100%"><span class="dd_head"><a href="javascript:ddwin_dispose()">X</a></span>'+H+"</div>");B.start({opacity:1,width:D,height:A}).chain(function(){G.setStyles({display:""});E.start({opacity:1})});ddw_visible=true}}function ddwin_prepare(){var B=$("dd_popup");var E=$("dd_content");var D=$("dd_host");var A=new Fx.Styles("dd_popup",{duration:500,transition:Fx.Transitions.Cubic.easeOut});var C=new Fx.Styles("dd_content",{duration:500,transition:Fx.Transitions.Cubic.easeOut});if(!ddw_visible){D.setStyles({display:""});B.setStyles({opacity:0});E.setStyles({opacity:1});E.setHTML('Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');A.start({opacity:1});ddw_visible=true}else{C.start({opacity:0}).chain(function(){E.setHTML('Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...');C.start({opacity:1})})}}var msgstack=[];var msglocked=false;function lockMessages(A){msglocked=A;if(!A){$each(msgstack,function(B,C){handleMessages(B)});msgstack=[]}}function handleMessages(C){if(msglocked){msgstack.push(C);return }var F="",J="";for(var E=0;E<C.count;E++){if($defined(C.message[E])){F=C.message[E][0];J=C.message[E][1];if(F=="MSGBOX"){window.alert(J)}else{if(F=="POPUP"){var B=310;if($defined(C.message[E][3])){B=C.message[E][3]}var D=(screen.width-B)/2;var I=120;if($defined(C.message[E][4])){D=C.message[E][4]}if($defined(C.message[E][5])){I=C.message[E][5]}createWindow(C.message[E][2],C.message[E][1],D,I,B)}else{if(F=="CALL"){var H=true;if($defined(C.message[E][2])){H=C.message[E][2]}gloryIO(J,false,H)}else{if(F=="NAVIGATE"){window.location="index.php?a="+J}else{if(F=="UPDATEGRID"){gloryIO("?a=map.grid.get&quick=1",false,true)}else{if(F=="RECT"){var A=$("grid_rect");try{if($defined(A)){if(J){rectinfo.w=1;rectinfo.h=1;rectinfo.bx=0;rectinfo.by=0;rectinfo.url="";rectinfo.clickdispose=true;rectinfo.silent=false;if($defined(C.message[E][2])){rectinfo.url=C.message[E][2]}if($defined(C.message[E][3])){rectinfo.w=C.message[E][3]}if($defined(C.message[E][4])){rectinfo.h=C.message[E][4]}if($defined(C.message[E][5])){rectinfo.bx=C.message[E][5]}if($defined(C.message[E][6])){rectinfo.by=C.message[E][6]}if($defined(C.message[E][7])){rectinfo.clickdispose=C.message[E][7]}if($defined(C.message[E][8])){rectinfo.silent=C.message[E][8]}A.setStyles({display:""})}else{A.setStyles({display:"none"})}}}catch(G){window.alert(G)}}else{if(F=="RANGE"){if($defined(C.message[E][1])){stackRegion(C.message[E][1])}}else{if(F=="POLLINTERVAL"){feeder_interval=C.message[E][1]}else{callback.call("message",C.message[E])}}}}}}}}}}}var data_io_time=0;function gloryIO(B,E,A,D){try{if(!A){showStatus('Loading...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/UI/mouseloading.gif" />')}data_io_time=$time();var C=new Json.Remote(B,{headers:{"X-Request":"JSON"},onComplete:function(J){showStatus();data_io_time=$time()-data_io_time;var K="NONE";if($defined(J.mode)){K=J.mode}if(K=="POPUP"){var I=100;var O=20;var H=310;if($defined(J.left)){I=J.left}if($defined(J.top)){O=J.top}if($defined(J.width)){H=J.width}createWindow(J.title,J.text,I,O,H)}else{if(K=="MAIN"){var Q=true;var L="";var N="";var P="";if($defined(J.rollback)){Q=J.rollback}if($defined(J.head_link)){L=J.head_link}if($defined(J.head_image)){N=J.head_image}if($defined(J.title)){P=J.title}nav_grid=[];overlay_grid=[];resetRegion();disposeDropDown();disposeActionPane();displayBuffer(J.text,Q,L,N,P)}else{if(K=="INFO"){}else{if(K=="FULL"){}else{if(K=="BLANK"){}else{if(K=="NONE"){}else{if(K=="GRID"){resetRegion();lockMessages(true);var Q=false;var L="";var N="";var P="";var G="none.gif";if($defined(J.rollback)){Q=J.rollback}if($defined(J.head_link)){L=J.head_link}if($defined(J.head_image)){N=J.head_image}if($defined(J.title)){P=J.title}if($defined(J.background)){G=J.background}grid_display.rollback=Q;grid_display.head_link=L;grid_display.head_image=N;grid_display.background=G;grid_display.title=P;if($defined(J.data)){overlay_grid=J.data}if($defined(J.nav)){nav_grid=J.nav}if($defined(J.x)){grid_x=J.x}if($defined(J.y)){grid_y=J.y}if($defined(J.map)){if(current_map!=J.map){loadGrid(J.map)}else{showStatus("Updating Grid");setTimeout(renderUpdate,100)}}else{setTimeout(renderUpdate,100)}}else{if(K=="DEDICATED"){var R=210;var H=400;if($defined(J.height)){R=J.height}if($defined(J.width)){H=J.width}try{ddwin_show(H,R,J.text)}catch(M){window.alert(M)}}else{if(K=="DROPDOWN"){if($defined(J.text)&&dropdownInfo.visible){$("dropdownLayer").setHTML(J.text)}}else{callback.call("ioreply",J)}}}}}}}}}if($defined(J.messages)){handleMessages(J.messages)}if(D){D(J)}},onFailure:function(G){if(!A){showStatus('<font color="red">Connection failure!</font>',1000)}if(D){D(false)}}}).send(E)}catch(F){if(!A){showStatus('<font color="red">Data Error!</font>',1000)}}}var data_grid=false;var collision_grid=false;var data_dictionary=false;var grid_range=false;var overlay_grid=false;var nav_grid=false;var current_map="";var glob_x_base=0;var glob_y_base=0;var grid_x=0,grid_y=0;var grid_display={rollback:false,head_link:false,head_image:false,title:false,background:"none.gif"};var rectinfo={w:3,h:3,bx:1,by:2,url:"",clickdispose:false,silent:false};function gridClick(A,B){gloryIO("?a=map.grid.get&x="+A+"&y="+B)}function loadGrid(B){current_map=B;showStatus("Loading Map...");var A=new Json.Remote("data/maps/"+B+".jmap",{onComplete:function(C){data_grid=C.grid;collision_grid=C.zid;data_dictionary=C.dic;grid_range=C.range;showStatus("Loading Graphics...");setTimeout(processDictionary,100)},onFailure:function(C){showStatus('<font color="red">Map Transaction Error!</font>',1000);data_grid=false;collision_grid=false;data_dictionary=false}}).send()}function processDictionary(){var A=new Array();for(img in data_dictionary){if($defined(img)){A.push("images/tiles/"+img)}else{A.push("images/tiles/blank.gif")}}data_dictionary=A;showStatus("Loading Graphics...");var B=0;new Asset.images(A,{onComplete:function(){showStatus("Updating Grid");setTimeout(renderUpdate,100);if(B>0){clearTimeout(B)}},onProgress:function(D){var C=Math.ceil(100*D/A.length);if(C>100){C-=100}showStatus("Loading Graphics ["+C+" %]");if(B>0){clearTimeout(B)}B=setTimeout(renderUpdate,2000)}})}function level2_sort(B,A){return B[0]-A[0]}function renderUpdate(){var C=24;var F=16;var G=grid_x-(C/2);var E=grid_y-(F/2);if(G<grid_range.x.m){G=grid_range.x.m}if(G+C>grid_range.x.M){G=grid_range.x.M-C}if(E<grid_range.y.m){E=grid_range.y.m}if(E+F>grid_range.y.M){E=grid_range.y.M-F}glob_x_base=G;glob_y_base=E;var D='<table cellspacing="0" cellpadding="0" id="tbl" style="background-image: url(images/tiles/'+grid_display.background+');">';for(var H=E;H<E+F;H++){D+="<tr>";for(var B=G;B<G+C;B++){D+="<td><div>";var A=new Array();if($defined(data_grid[H])){if($defined(data_grid[H][B])){$each(data_grid[H][B],function(I,J){A.push([J,data_dictionary[I]])})}}if($defined(overlay_grid[H])){if($defined(overlay_grid[H][B])){$each(overlay_grid[H][B],function(I,J){A.push([J,"images/tiles/"+I])})}}if(A.length>0){A.sort(level2_sort);A.each(function(I){D+='<img src="'+I[1]+'">'})}D+="</div></td>"}D+="</tr>"}D+="</table>";D+='<div id="grid_rect" class="dbf_container" style="border-width: 2px; border-style: solid; border-color: #FF0000; position: absolute; display: none"></div>';displayBuffer(D,grid_display.rollback,grid_display.head_link,grid_display.head_image,grid_display.title);showStatus();lockMessages(false)}var hoverInfo={text:"",x:0,y:0,sz:{x:0,y:0}};function hoverShow(C,A,D){var B=$("hoverLayer");if(C){if(hoverInfo.text!=C){hoverInfo.text=C;B.setHTML(C);hoverInfo.sz=B.getSize().size;B.setStyles({visibility:"visible"})}if(hoverInfo.x!=A||hoverInfo.y!=D){hoverInfo.x=A;hoverInfo.y=D;B.setStyles({left:A-(hoverInfo.sz.x/2),top:D-hoverInfo.sz.y-12})}}else{if(hoverInfo.text!=""){hoverInfo={text:"",x:0,y:0,sz:{x:0,y:0}};B.setStyles({visibility:"hidden"})}}}var dropdownInfo={visible:false};function dropdownShow(A,D,B){var C=$("dropdownLayer");C.setHTML('<img src="images/UI/loading2.gif" align="absmiddle" />');C.setStyles({visibility:"visible",left:A+5,top:D+5});dropdownInfo.visible=true;gloryIO("?a=interface.dropdown&guid="+B,false,true)}function disposeDropDown(){var A=$("dropdownLayer");if(dropdownInfo.visible){A.setStyles({visibility:"hidden"});dropdownInfo.visible=false}}var regions=[];var visibleRegionID=-1;var activeEvent=false;function stackRegion(A){regions.push(A)}function resetRegion(){disposeActionPane();regions=[];visibleRegionID=-1}function hitTestRegion(A,C){if(visibleRegionID>-1){return }for(var B=0;B<regions.length;B++){if((regions[B].show.x==A)&&(regions[B].show.y==C)){setTimeout(function(){showRegion(B)},100);return }}}function showRegion(A){renderActionRange(regions[A]);visibleRegionID=A;hoverShow(false)}function disposeActionPane(){if(visibleRegionID==-1){return }var A=$("actionpane");var B=new Fx.Styles(A,{duration:500,transition:Fx.Transitions.Cubic.easeOut});B.start({opacity:0}).chain(function(){A.setStyles({visibility:"hidden"})});visibleRegionID=-1}function renderActionRange(B){try{var A=0;var E=0;var D='<table cellspacing="0" cellpadding="0">';for(E=B.y.m;E<=B.y.M;E++){D+="<tr>";for(A=B.x.m;A<=B.x.M;A++){if($defined(B.grid[E])){if($defined(B.grid[E][A])){D+='<td><a href="javascript:void(0);" onclick="disposeDropDown();gloryIO(\'?a='+B.action+"&id="+B.grid[E][A].i+'\');" class="actgrid_link" style="background-color: '+B.grid[E][A].c+'">';if($defined(B.grid[E][A].t)){D+=B.grid[E][A].t}else{D+="&nbsp;"}D+="</a></td>"}else{D+='<td><div class="actgrid_div">&nbsp;</div></td>'}}else{D+='<td><div class="actgrid_div">&nbsp;</div></td>'}}D+="</tr>"}D+="</table>";renderActionPane(D,B.point.x-glob_x_base,B.point.y-glob_y_base)}catch(C){window.alert(C)}}function renderActionPane(D,A,G){var B=$("actionpane");var F=$("datapane").getLeft();var E=$("datapane").getTop();B.setHTML(D);B.setStyles({visibility:"visible",opacity:0,left:(A*32-12)+F,top:(G*32-12)+E});B.addEvent("click",function(H){H=new Event(H);H.stop()});B.addEvent("mousemove",function(H){H=new Event(H);H.stop()});B.addEvent("mouseleave",function(H){H=new Event(H);disposeActionPane();H.stop()});var C=new Fx.Styles("actionpane",{duration:500,transition:Fx.Transitions.Cubic.easeOut});C.start({opacity:0.8}).chain(function(){hoverShow(false)})}var feeder_interval=5000;var feeder_timer=0;var feeder_enabled=true;var iD=0;function feeder(){iD++;$("prompt").setHTML("Feeded: "+iD);gloryIO("msgfeed.php",false,true,function(A){if(feeder_timer){clearTimeout(feeder_timer)}if(feeder_enabled){feeder_timer=setTimeout(feeder,feeder_interval)}})}var hoveredItem=false;$(window).addEvent("load",function(A){initWaiter();initDisplayBuffer();$("datapane").addEvent("mousemove",function(G){G=new Event(G);var K=$("datapane").getLeft();var J=$("datapane").getTop();var C=Math.ceil((G.event.clientX-K)/32)-1;var H=Math.ceil((G.event.clientY-J)/32)-1;var I=C+glob_x_base;var E=H+glob_y_base;var D="";var F="";hitTestRegion(I,E);if($defined(nav_grid[I])){if($defined(nav_grid[I][E-1])){D=nav_grid[I][E-1];F=nav_grid.dic[D]}}if(F.d){$("prompt").setHTML("X: "+I+", Y: "+E+" With Zero at: "+glob_x_base+","+glob_y_base+", Overlay: "+D+" Dic:"+F.d.name);hoveredItem=F;hoverShow(F.d.name,G.event.clientX,G.event.clientY)}else{$("prompt").setHTML("X: "+I+", Y: "+E+" With Zero at: "+glob_x_base+","+glob_y_base);hoveredItem=false;hoverShow(false)}var B=$("grid_rect");if(B){if(B.getStyle("display")!="none"){B.setStyles({left:(C-rectinfo.bx)*32,top:(H-rectinfo.by)*32,width:rectinfo.w*32,height:rectinfo.h*32,display:""})}}});$("datapane").addEvent("contextmenu",function(B){B=new Event(B);if(hoveredItem!=false){dropdownShow(B.event.clientX,B.event.clientY,hoveredItem.g)}else{disposeDropDown()}B.stop()});$("datapane").addEvent("click",function(D){D=new Event(D);var F=$("datapane").getLeft();var E=$("datapane").getTop();var B=Math.ceil((D.event.clientX-F)/32)+glob_x_base-1;var C=Math.ceil((D.event.clientY-E)/32)+glob_y_base-1;if(rectinfo.url==""){hitTestRegion(B,C)}else{gloryIO(rectinfo.url+"&x="+B+"&y="+C);if(rectinfo.clickdispose){rectinfo.url="";$("grid_rect").setStyles({display:"none"})}}disposeDropDown()});gloryIO("?a=map.grid.get");feeder()});$(window).addEvent("focus",function(A){return ;feeder_enabled=true;if(feeder_timer==0){feeder_timer=setTimeout(feeder,1000)}});$(window).addEvent("blur",function(A){return ;feeder_enabled=false;feeder_timer=0});$(window).onerror=function(B){if($defined(showStatus)){try{showStatus('<font color="red">Script Error!</font><br /><small>'+B+"</small>",1000)}catch(A){window.alert(B)}}else{window.alert(B)}};$(window).addEvent("keydown",function(B){B=new Event(B);if(B.code==27){var A=$("grid_rect");if(A){if(A.getStyle("display")!="none"){A.setStyles({display:"none"});rectinfo.url=""}}disposeDropDown();B.stop()}});function display(A){gloryIO("index.php?"+A)};