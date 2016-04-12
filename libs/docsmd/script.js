function form_center(form){

    var w = document.documentElement.clientWidth,
        h= document.documentElement.clientHeight,
        w1 = form.clientWidth,
        h1 = form.clientHeight;

    form.style.left= Math.floor((w-w1)/2)+'px';
    form.style.top=Math.floor((h-h1)/2)+'px';
}
    


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
    
    var comments_inner = comments.querySelector('.comments-inner');
    
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
    
    
    var self = this;
    var form = null;

    var formText = 'Ваше сообщение:<br>'+
        '<textarea rows="10" cols="100" name="message" class="user-message" required>Ответ на вопрос</textarea><br>'+
        'item_id <input name="item_id"><br>'+
        'replay_to <input name="replay_to"><br>'+
        'user_id <input name="user_id"><br>'+
        'topic_name <input name="topic_name"><br>'+
        '<div style="float:right;">'+
        '<input type="submit" value="Отправить">'+
        '<input type="reset" value="Отмена">'+
        '</div>';

    /**
     * Форма редактирования сообщений
     * @param {type} parent
     * @param {type} callback
     * @param {type} message
     * @returns {DocManager.form|Element}
     */    
    function editForm(parent,callback,message){
        form = document.createElement('form');
        form.innerHTML=formText;
        form.className='form-message';
        form.user_id.value=user_id;
        form.topic_name.value=page;   
        if (typeof message !=='undefined'){
            form.message.innerHTML = message;
        }
        
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
        
        return form;
    }
    
    /**
     * Позиционирование окна на указанное сообщение
     * @param {type} item_id
     * @returns {undefined}
     */
    this.goup =function(element){
        var item_id = element.getAttribute('data-replay-to');
        
        for(var i=0;i<comments.children.length;i++){
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
    


    /**
     * Форма для добавления аттача
     * @param {type} element
     * @param {type} item_id
     * @param {type} callback
     * @returns {undefined}
     */
    function uploadForm(element,callback){
        var form = document.createElement('form');
        form.className='upload_screenshort';
        form.innerHTML= '<div style="padding:10px;"><div><input type="file" name="screenshort" required></div></div>'
                       +'<div style="float:right;">'
                       +'<input type="submit" value="Загрузить">'
                       +'<input type="reset" value="Отмена">'
                       +'</div>';
        element.appendChild(form);
        form_center(form);
        
        form.onsubmit = function(){
            callback(new FormData(this));
            element.removeChild(form);
            form = null;
            return false;
        };
        
        form.onreset = function(){
            form=null;
            element.removeChild(form);
            return false;
        };
        
    }

    
    /**
     * Получить заголовок сообщения пользователя
     * @param {type} item_id
     * @param {type} callback
     * @returns {undefined}
     */
    this.get_comment_header = function (item_id,callback){
        var request = Request(function(text){
            console.log(text);
            var a = JSON.parse(text);
            if (a['error']===0){
                callback(a['header']);
            } else {
                alert()
            }
        });
        request.open('POST',php_path+'/proc.php');
        request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        request.send('command=comment-header&item_id='+item_id);
        
    }
    
    
    
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
    
    /**
     * Новое сообщение пользователя
     * @returns {undefined}
     */
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
            request.open('POST',php_path+'proc.php');
            request.send(data);
            
        });
        form_center(form);
    };
    
    /**
     * Пользователь хочет редактировать своё сообщение
     * @param {type} comment_item
     * @returns {undefined}
     */
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
            form_center(form);
        });
        
        request.open('POST',php_path+'proc.php');
        request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        request.send('command=quotes&item_id='+comment_id);
        
    };
    
    /**
     * Пользователь хочет ответить на сообщение другого пользователя
     * @param {type} comment_item
     * @returns {undefined}
     */
    this.replay_comment=function(comment_item){        
        var comment_id = comment_item.children[0].getAttribute('data-comment-id');
        self.get_comment_header(comment_id,function(header){
        
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

            },header);

            form.replay_to.value=comment_id;
            form_center(form);
        });
    };
    
    /**
     * Пользователь хочет удалить своё сообщение
     * @param {type} comment_item
     * @returns {undefined}
     */
    this.remove_comment=function(comment_item){
        
        var comment_id = comment_item.children[0].getAttribute('data-comment-id');
        
        if (confirm('Удалить сообщение '+comment_id)){
        
            var request= Request(function(text){
                console.log(text);
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

    /**
     * Перезагрузка сообщения
     * @param {type} comment_item
     * @returns {undefined}
     */
    this.reload_item=function(comment_item){
        var item_id=comment_item.children[0].getAttribute('data-comment-id');
        var request = Request(function(text){
//            var d = comment_item.parentElement;
            comment_item.innerHTML =text;
//            alert(text);
        });
        request.open('POST',php_path+'proc.php');
        request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        request.send('command=reload-item&item_id='+item_id);
        
    };
    
    /**
     * Пользователь добавляет аттачмент к своему сообщению
     * @param {type} comment_item
     * @returns {undefined}
     */
    this.add_attachment=function(comment_item){
        var item_id=comment_item.children[0].getAttribute('data-comment-id');
        uploadForm(comment_item,function(data){
            var request = Request(function(text){
                console.log(text);
                var a=JSON.parse(text);
                if (a['error']===0){
                    reload_item(comment_item);
                } else {
                    alert(text);
                }    
                
            });
            request.open('POST',php_path+'proc.php');
            data.append('command','upload-attach');
            data.append('item_id',item_id);
            request.send(data);
        });
        return false;
    };
    
    /**
     * Пользователь удаляет аттачмент своего сообщения
     * @param {type} comment_item
     * @returns {undefined}
     */
    this.delete_attachment=function(comment_item,image_id){
        if (confirm('Удалить прикреплённый файл')){
            var request = Request(function(text){
                console.log(text);
                reload_item(comment_item);
            });
            request.open('POST',php_path+'proc.php');
            request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            request.send('command=delete-attachment&image_id='+image_id);
        }
    };
    
    /**
     * Обработсчик кнопок сообщения
     *   добавить<br>
     *   изменить<br>
     *   ответить<br>
     *   удалить<br>
     *   
     *   прикрепить файл<br>
     *   удалить файл<br>
     *     
     * @param {type} event
     * @returns {Boolean}
     */
    this.comments_button_click=function(event){
        
        if (user_id<=0){
            alert('Необходимо войти');
            return false;
        }
        
        if (form===null){
            var target = event.target;
            var action = target.getAttribute('data-action');
            console.log(' action ->'+action);
            if (action==='delete_attachment'){
                $image_id = target.closest('.attach').getAttribute("data-attach-id");
                self.delete_attachment(this,$image_id);
            } else {
                self[action](this);
            }
        }
    };
    
    /**
     * Добаление кнопок к сообщению
     * @param {type} comments_item
     * @returns {undefined}
     */
    this.add_comments_button=function(comments_item){
        
        var buttons = document.createElement('div');
        buttons.className = 'comment-buttons';
        
        var button;
        var issomeuser = user_id == comments_item.children[0].getAttribute('data-user-id');
        var isadmin = (role_id===3);
        
        if (issomeuser){
            button = document.createElement('button');
            button.innerHTML='Прикрепить файл';
            button.setAttribute('data-action','add_attachment');
            buttons.appendChild(button);
        }
        
        if (issomeuser || isadmin){
            button = document.createElement('button');
            button.innerHTML='Изменить';
            button.setAttribute('data-action','edit_comment');
            buttons.appendChild(button);
        }
        
        if (!comments_item.classList.contains('replay-to') && !issomeuser){
        
            button = document.createElement('button');
            button.innerHTML='Ответить';
            button.setAttribute('data-action','replay_comment');
            buttons.appendChild(button);
        }
        
        if (issomeuser || isadmin){
            button = document.createElement('button');
            button.innerHTML='Удалить';// ('+comments_item.children[0].getAttribute('data-user-id')+')';
            button.setAttribute('data-action','remove_comment');
            buttons.appendChild(button);
        }
        
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
    
    /**
     * Чтение списка сообщений по теме
     * @returns {undefined}
     */
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