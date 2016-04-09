function Request(callback){
    var request = new XMLHttpRequest();
    request.onreadystatechange=function(){
        if (request.readyState===4){
            switch (request.status){
                case 200:
                    callback(request.responseText);
                    return;
                case 404:
                    alert('Страница не найдена');
                    return;
                default:
                    alert(request.status+' Ошибка Request');
            }
        }
    };
    return request;
}


/**     Поиск по документации 
 * 
 **/
function Search(search_form,result,php_path){
    if (search_form!==null){

        search_form.onsubmit = function(){
            var request = Request(function(){
                result.innerHTML = request.responseText;
            });
            request.open('POST', php_path+'/search.php');
            var data = new FormData(this);
            data.append('command','search');
            request.send(data);
            return false;
        };
    }

}

/**
 * Редактор страниц и файлов конфигурации
 * @param {type} element
 * @param {type} options
 * @returns {Editor}
 * 
 *   <b>options</b>
 *    contenttpl  : '<?=CONTENT_TPL?>',<br>
 *    page        : '<?=$page?>.md',<br>
 *    linktpl     : '<?=LINK_TPL?>',<br>  
 *    contentlink : '<?=$server_link.'/md/'?>',<br>
 *    upload_php  : '<?=$php_path.'upload.php'?>'<br>
 * 
 * 
 * 
 */

function Editor(element,options){
                    
    var contenttpl  = options['contenttpl'];
    var page        = options['page'];
    var linktpl     = options['linktpl'];
    var contentlink = options['contentlink'];
    var php_path  = options['php_path'];

    var form;
    var self=this;

    this.save = function(){
        var request = Request( function(text){
            console.log(text)
            var a = JSON.parse(text);
            if (a['error']===0){
                document.body.removeChild(form);
                location.reload();
                return;
            };
            alert(text);

        });

        request.open('POST',php_path+'proc.php');
        request.send(new FormData(form));
    };

    this.cancel = function(){
        document.body.removeChild(form);
    };


    function execute(filename,text){
        form = document.createElement('form');
        form.className='editor';
        form.innerHTML =
                '<div><input name="filename"><input name="command"></div>'
                +'<div><textarea name="text" cols="100" rows="25"></textarea></div>'
                +'<div style="float:right;"><button data-action="save">Сохранить</button>'
                +'<button data-action="cancel">Отмена</button></div>';
        form.filename.value=filename;
        form.command.value="upload_page";
        form.text.innerHTML = text;
        form.onclick=function(event){
            var target = event.target;
            if (target.tagName==='BUTTON'){
                var action = target.getAttribute('data-action');
                self[action]();
                return false;
            }
            return;
        };            
        document.body.appendChild(form);
        return form;
    };

    function p1(page){
        var request= Request(function(responseText){
            execute(page,responseText);

        });
        request.open('GET', contentlink+page+'?x='+Math.random());
        request.send();

    }    

    this.edit_page=function(){
        p1(page);
    };

    this.edit_content=function(){
        p1(contenttpl);
    };

    this.edit_link=function(){
        p1(linktpl);
    };


    element.onclick= function(event){
        var target = event.target;
        if (target.tagName==='A'){
            var action =target.getAttribute('data-action');
            self[action]();
            return false;
        };
    };
}



