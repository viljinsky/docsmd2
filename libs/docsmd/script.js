/**
 * На документе должна быть форма класса auth
 * @param {type} comments
 * @param {type} options
 * 
 *     php_path
 *     user_id
 *     role_id
 *     
 * @returns {DocManager}
 */
function DocManager(comments,options){
    
    var php_path=options['php_path'];
    var user_id=options['user_id'];
    var role_id = options['role_id'];
    
    var page = 'index';
    var serch = location.search;
    if (serch!==''){
        var a = serch.slice(1).split('&');
        for (i in a){
            var p = a[i].split('=');
            if (p[0]==='page'){
                page = p[1];
                break;
            }
        }
    }
//    console.log('page : '+page+' user_id : '+user_id+' role_id : '+role_id);
    
    
    var self = this;
    var form = null;

    var formText = 'Ваше сообщение:<br>'+
        '<button class="quotes">цитировать</button>'+
        '<button class="screenshort">скриншорт</button><br>'+
        '<textarea rows="10" cols="100" name="message" class="user-message" required>Ответ на вопрос</textarea><br>'+
//        '<div style="display:none;">'+
        'item_id <input name="item_id"><br>'+
        'replay_to <input name="replay_to"><br>'+
        'user_id <input name="user_id"><br>'+
        'topic_name <input name="topic_name"><br>'+
//        '</div>'+
        '<div style="float:right;">'+
        '<input type="submit" value="Отправить">'+
        '<input type="reset" value="Отмена">';

    function editForm(parent,callback){
        form = document.createElement('form');
        form.innerHTML=formText;
        form.className='form-message';
        form.user_id.value=user_id;
        form.topic_name.value=page;   
        
        form.onsubmit = function(){
            callback(new FormData(this));
            parent.removeChild(form);
            form=null;
            return false;
        };
        
        form.onreset = function(){
            parent.removeChild(form);
            form=null;
            return false;
        };
        
        parent.appendChild(form);
        
        form.querySelector('.screenshort').onclick=function(){
            add_screen_short(form,form.item_id.value,function(){
                alert('OK');
            });
            return false;
        };
        return form;
    }
    
    /**
     * Позиционирование окна на указанное сообщение
     * @param {type} item_id
     * @returns {undefined}
     */
    this.goup =function(element){
        var item_id = element.getAttribute('data-replay-to');
        
        for(i=0;i<comments.children.length;i++){
            var el = comments.children[i];
            if (el.hasAttributes('data-id') && el.getAttribute('data-id')==item_id){
                if (el!==null){
                    var r = el.getBoundingClientRect();
                    window.scrollBy(0,r.top);
                }
                break;
            }
        }
    };
    

    // Функции добавить цитату и скриншорт


    function send_screenshort(form,callback){
        var request = Request(function(text){
            console.log(text);
            $a = JSON.parse(text);
            if ($a['error']===0){
                callback($a['image_id'],$a['filename'],$a['src']);
                return;
            }
            alert('Какая-то ошибка!\n'+text);
            
        });
        request.open('POST',php_path+'proc.php' );//'add_screenshort.php');
        request.setRequestHeader('enctype','multipart/form-data');
        request.send(new FormData(form));
    }

    function add_screen_short(element,item_id,callback){
        var form = document.createElement('form');
        form.method='POST';
        form.className='upload_screenshort';
        form.action=php_path+'proc.php';//'add_screenshort.php';
        form.enctype="multipart/form-data";
        
        form.innerHTML= '<div><input type="file" name="screenshort" required></div>'
                       +'<input name="command">' 
                       +'item_id <input name="item_id">' 
                       +'<div style="float:right;">'
                       +'<input type="submit" value="Загрузить">'
                       +'<button data-action="close">Отмена</button>'
                       +'</div>';
               
        form.item_id.value = item_id;// element.item_id.value;       
        form.command.value='upload_attach';
        element.appendChild(form);
        form.onclick=function(event){
            if (event.target.tagName==='BUTTON'){
                element.removeChild(form);
                form=null;
            }
        };
        
        form.onsubmit = function(){
            send_screenshort(form,function(image_id,filename,src){
                var user_message = element.querySelector(".user-message");
                
                var message = user_message.innerHTML
                        +"\n["+filename+"]("+src+")\n";
                
                user_message.innerHTML = message;
               
//                msg.innerHTML = msg.innerHTML+"<br>"+'рисунок '+filename+'<br>'+
//                        '<img src="'+src+'" alt="'+filename+'" title="'+filename+'">';
                
                
            });
            element.removeChild(form);
            return false;
        };
    }
    
    
    
    
    
    //--------------------   new ----------------------
    
//    function Request(callback){
//        var request = new XMLHttpRequest();
//        request.onreadystatechange=function(){
//            if (request.readyState===4){
//                switch (request.status){
//                    case 200:
//                        callback(request.responseText);
//                        return;
//                    case 404:
//                        alert('Страница не найдена');
//                        return;
//                    default:
//                        alert(request.status+' Ошибка Request');
//                }
//            }
//        };
//        return request;
//    }
//    
    
    var comments_inner = comments.querySelector('.comments-inner');
    
    this.add_comment=function(){
        var comment_item = document.createElement('div');

        var message = document.createElement('div');
        message.className = 'comment-item';
        message.innerHTML='Новое сообщение';

        comment_item.className='comments-container';
        comment_item.setAttribute('data-comment-id','-1');

        comment_item.appendChild(message);
        comments_inner.appendChild(comment_item);
        self.add_comments_button(comment_item);
        comments_inner.appendChild(comment_item);
        return comment_item;
    };
    
    this.append_comment=function(){

        if (user_id<0){
            alert('Необходимо войти');
            return;
        }

        form = editForm(document.body,function(data){
            
            var request = Request(function(text){
                
                var d = document.createElement('div');
                d.style.position='relative';
                d.innerHTML=text;
                comments_inner.appendChild(d);
                self.add_comments_button(d);
            });

            data.append('command','add');
            request.open('POST',php_path+'proc.php');//'add_message.php');
            request.send(data);
            
        });
    };
    
    this.edit_comment=function(comment_item){
        
        var comment_id = comment_item.children[0].getAttribute('data-comment-id');
        
        var request = Request(function(text){
            
            form = editForm(document.body,function(data){
                var r = Request(function(text){
                    comment_item.innerHTML = text;
                    self.add_comments_button(comment_item);
                });
                
                data.append('command','edit');
                r.open('POST',php_path+'proc.php');//'edit_message.php');
                r.send(data);
                
            });
            
            form.item_id.value=comment_id;
            form.user_id.value=comment_item.children[0].getAttribute('data-user-id');
            form.message.innerHTML=text.replace(/<br>/g,"\n").replace(/\r/g,""); 
            document.body.appendChild(form);
        });
        
        request.open('POST',php_path+'proc.php');
        request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        request.send('command=quotes&item_id='+comment_id);
        
    };
    
    this.replay_comment=function(comment_item){        
        var comment_id = comment_item.children[0].getAttribute('data-comment-id');
        form = editForm(document.body,function(data){
            
            var request = Request(function(text){
                var d = document.createElement('div');
                d.style.position='relative';
                d.innerHTML=text;
                comments_inner.insertBefore(d,comment_item.nextElementSibling);
                self.add_comments_button(d);
            });
            
            data.append('command','replay');
            request.open('POST',php_path+'proc.php');
            request.send(data);
            
        });
        
        form.replay_to.value=comment_id;
    };
    
    this.remove_comment=function(comment_item){
        
        var comment_id = comment_item.children[0].getAttribute('data-comment-id');
        
        if (confirm('Удалить сообщение '+comment_id)){
        
            var request= Request(function(text){
                var a = JSON.parse(text);
                if (a['error']===0){
                    comments_inner.removeChild(comment_item);
                } else {
                    alert(a['message']);
                }
            });
            
            request.open('POST',php_path+'proc.php');
            request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            request.send('command=delete&item_id='+comment_id);
        }
    };
    
    this.comments_button_click=function(event){
        
        if (user_id<0){
            alert('Необходимо войти');
            return false;
        }
        
        if (form===null){
            var target = event.target;
            var action = target.getAttribute('data-action');
            self[action](this);
        }
    };
    
    this.add_comments_button=function(comments_item){
        
        var buttons = document.createElement('div');
        buttons.className = 'comment-buttons';
        
        var button;
        
        button = document.createElement('button');
        button.innerHTML='Изменить';
        button.setAttribute('data-action','edit_comment');
        buttons.appendChild(button);
        
        if (!comments_item.classList.contains('replay-to')){
        
            button = document.createElement('button');
            button.innerHTML='Ответить';
            button.setAttribute('data-action','replay_comment');
            buttons.appendChild(button);
        }
        
        button = document.createElement('button');
        button.innerHTML='Удалить';
        button.setAttribute('data-action','remove_comment');
        buttons.appendChild(button);
        
        comments_item.appendChild(buttons);
        comments_item.onclick=self.comments_button_click;

        
        
//        buttons.style.display = 'none';
//        
//        comments_item.onmouseenter= function(){
//            buttons.style.display = 'block';
//        };
//        comments_item.onmouseleave = function(){
//            buttons.style.display = 'none';
//        }
       
        
    };
    
    this.read_comments = function(){
        
        var request = Request(function(response){
            comments.innerHTML = response;
            comments_inner=comments.querySelector('.comments-inner');
            if (role_id>0){
                
                for (i=0;i<comments_inner.children.length;i++){
                    self.add_comments_button(comments_inner.children[i]);
                }
            
                var button=document.createElement('button');
                button.innerHTML='Новое сообщение';
                button.onclick = self.append_comment;
                comments.appendChild(button);
            }
        });
        request.open('POST',php_path+'proc.php');
        request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        request.send('command=read&page='+page);
        
    };
    
    this.read_comments();    

}