<?
    require("includes/configure.php");

    $r = mysql_query("SELECT folder_id AS id, folder_name AS name FROM folders ORDER BY sort ASC");
    while ($row = mysql_fetch_assoc($r)) {
        $folders[$row['id']] = $row;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>File API</title>
    <link rel="stylesheet" type="text/css" href="css/compiled/estilo.css">
    <link rel="stylesheet" type="text/css" href="css/vendors/overcast/jquery-ui.css">
    <meta charset="UTF-8">
    <style>
        .base{
            display: none;
        }
    </style>
</head>
<body>
    <span id="start_upload">Subir imágenes</span>
    <div class="file_manage">
        <div class="fileButton bigButton">
            <input type="file" multiple id="inputFile">
            Buscar Archivo...
        </div>
        <div id="dropzone" class="interactive_area">
            
           <div class='thumb base' >
                <span class="facebook enable">f</span>
                <span class="close">x</span>
                <img class="image" id="test" src="">
                <span class='button upload'>up</span>
                <span class='button info'>i</span>
            </div>
        </div> 

        <div id="picture_info" class="hide">
            <form action="">
                <label for="in_facebook">
                    <input type="checkbox" name="in_facebook">
                    Enviar a facebook
                </label>
                <br>
                <label for="desc">Descripción</label>
                <textarea name="desc" id="desc" cols="40" rows="4"></textarea>
            </form>
        </div>
    </div>
    <div class="folder_manage">
        <div id="new_folder" class="bigButton">Nuevo Album</div>
        <div class="interactive_area" id="folderzone">
            <div class="folder thumb base">
                <div class="content">
                    <? // <img class="image" id="test" src="http://lorempixel.com/400/200/nature/"> ?>
                </div>
                <span class='button info'>i</span>
            </div>

            <? foreach ($folders as $key => $value) { ?>
                <div class="folder thumb" data-id="<?=$key?>" title="<?=$value['name']?>">
                    <div class="content">
                        <? // <img class="image" id="test" src="http://lorempixel.com/400/200/nature/"> ?>
                    </div>
                    <span class='button info'>i</span>
                </div>
            <? } ?>

        </div>
    </div>

    <div id="progressbar" class="overlay hide">
        <div class="bar"><div class="progress-label">Loading...</div></div>
    </div>

<script type="text/javascript" src="js/vendors/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/vendors/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="js/vendors/notify.min.js"></script>
<script>

var     zone = document.getElementById('dropzone');

if (typeof window.FileReader === 'undefined') {
  alert("no funciona contigo, usa chrome")
} 
 
zone.ondragover = function () { $(this).addClass('hover'); return false; };
zone.ondragend = function () { $(this).removeClass('hover'); return false; };
zone.ondrop = addFiles;

function addFiles (e) {
    e.preventDefault();

    console.log(e);
      

    if(e.dataTransfer !== undefined) //drop action
        var list = e.dataTransfer.files;
    else //input action
        var list = e.target.files;

    for(var index = 0; index < list.length; index++){
        var file = list[index];
        var reader = new FileReader();

        reader.onload = readImg;
        console.log(file);
        //reader.readAsDataURL(file);
        reader.readAsArrayBuffer(file);
    }

    return false;
}

function readImg(event) {
    console.log(event.target);
    var blob = new Blob([event.target.result]);
    var url = window.URL.createObjectURL(blob);
    //var thumb = $("<div class='thumb'><img class='image' src='"+url +"'><span class='button upload'>up</span></div>");
    var thumb = $("#dropzone .thumb.base").clone().removeClass("base");
    thumb.data("info",{in_facebook:1,desc:''});
    thumb.draggable({
        revert: true,
        cursorAt: { left: -15, top: -16 },
        containment: $("body"),
        helper:function(e){
            var thumb = e.currentTarget;
            return $(".image",thumb).clone();
        }
    });

    $("img",thumb)[0].src = url;

    $(".image",thumb)[0].onload = function(){
        console.log(2);
        if($(this).data("canvas") === undefined){
            var c = resizeImg(this);
            
            //addLogo(c,"img/wmark.png",{x:20,y:-20},{height:70});

            //$("body")[0].appendChild(c);

            $(this).data("canvas",c);
            this.src = c.toDataURL("image/jpeg",1);
        }
    };
    $(zone).append(thumb);

  };

$(function(){
    var color_cnt = {
        i:0,
        maxI: 4,
        getI : function(){
            this.i++
            if(this.i>this.maxI)
                this.i = 1;
            return this.i;
        }

    };

    $(".folder").each(function(i,e){
        var color = "color"+color_cnt.getI();
        $(e).attr("data-class",color);
        $(e).addClass(color);
    });

    $("#inputFile").change(addFiles);

    $("#dropzone .thumb.base").draggable({
        revert: true,
        cursorAt: { left: -15, top: -16 },
        containment: $("body"),
        helper:function(e){
            var thumb = e.currentTarget;
            return $(".image",thumb).clone();
        }
    });

    var drop_opt = {
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function(e,ui){
            console.log('drop',arguments,this);

            ui.draggable.removeClass(ui.draggable.attr("data-class"));
            ui.draggable.attr("title","Album: "+$(this).attr("title"));
            ui.draggable.attr("data-class",$(this).attr("data-class"));
            ui.draggable.addClass($(this).attr("data-class"));

            ui.draggable.data("folder",{
                name: $(this).attr("title"),
                id: $(this).attr("data-id")
            });
        }
    };

    $(".folder").droppable(drop_opt);

    $("#dropzone").on("click",".upload",{},function(){
        var cont = $(this).parents(".thumb");
        console.log("up");
        $.post(
            'uploadImg.php',
            {
                imgData: $("img",cont).data("canvas").toDataURL("image/jpeg",0.7)
            },
            function(e){
                console.log("complete", e);
            }
        )
    });

    $("#dropzone").on("click",".info",{},function(){
        console.log("info")
        var pic = $(this).parent();
        var data = pic.data("info") || {};

        if(data.in_facebook == 1){
            $("#picture_info [name=in_facebook]")[0].checked = true;
        }else{
            $("#picture_info [name=in_facebook]")[0].checked = false;
        }
        $("#picture_info [name=desc]").val(data.desc || '');

        $("#picture_info").dialog({
            autoOpen: true,
            //height: 300,
            title: "Información de la imágen",
            width: 400,
            modal: true,
            buttons: {

                Guardar: function(){
                    console.log(this);

                    var data = {
                        in_facebook: $("[name=in_facebook]",this)[0].checked ? 1 : 0,
                        desc : $("[name=desc]",this).val()
                    }

                    console.log(pic,pic.find(".facebook"));
                    if(data.in_facebook){
                        pic.find(".facebook").addClass("enable");
                    }else{
                        pic.find(".facebook").removeClass("enable");
                    }

                    pic.data("info",data);
                    console.log(pic.data("info"));
                    $( this ).dialog( "close" );
                },
                Cancelar: function(){
                    $( this ).dialog( "close" );
                }

            }
        });
    });

    $("#dropzone").on("click",".close",{},function(){
        $(this).parent().hide("slow").remove();
    });

    $("#new_folder").click(function(){
        var name = prompt("Nombre del nuevo album",'');
        if(name!==null){
            $.post(
                "ajax_calls.php",
                {
                    action:'new_folder',
                    data:{
                        name : name
                    }
                },
                function(data){
                    var color = "color"+color_cnt.getI();

                    var folder = $(".folder.base").clone().removeClass("base");
                    folder.attr("title",name)
                        .attr("data-class",color)
                        .addClass(color)
                        .droppable(drop_opt)
                        .appendTo("#folderzone");
                }
            );
        }
    });
    $("#folderzone").on("click",".info",{},function(){
        var name = prompt("Cambiar nombre del album",$(this).parents(".folder").attr("title"));
        if(name!==null){
            $(this).parents(".folder").attr("title",name);
            $.post(
                "ajax_calls.php",
                {
                    action:'edit_folder',
                    data: {
                        name: name,
                        id : $(this).parents(".folder").attr("data-id")
                    }
                });
        }
    });

    $("#start_upload").click(function(){
        if(!$(this).attr("disabled")){
            console.log("a subir");

            var progressbar = $( "#progressbar .bar" ),
            progressLabel = $( "#progressbar .progress-label" );

            $("#progressbar").show('medium');
    

            var max = $("#dropzone .thumb").length-1;
            progressbar.progressbar({
              value: false,
              max: max,
              change: function() {
                progressLabel.text( progressbar.progressbar( "value" ) + " de " + max );
              },
              complete: function() {
                progressLabel.html( "Carga completada, <a href='#' onClick='window.location.reload()'>Cargar mas</a>" );
              }
            });

            $("#dropzone .image").each(uploadImg);
        }
    });

    //$(document).on("load",".thumb .image",{},resizeImg);
});

function uploadImg(i,img){
    console.log(arguments);
    var $thumb = $(img).parents(".thumb");
    var info = $thumb.data("info");

    if($thumb.hasClass("base")){
        return null;
    }

    console.log('upload',img);
    $.ajax({
        url: 'uploadImg.php',
        cache: false,
        context: $thumb[0],
        data:{ imgData: $(img)[0].src, info:info, folder:$thumb.data("folder") },
        beforeSend: function(){
            $(this).append('<div class="bar"></div>');
            $('.bar',this).progressbar({value:false});
        },
        complete:  function(e,status){
            $('.bar',this).remove();
            $( "#progressbar .bar" ).progressbar("value",$( "#progressbar .bar" ).progressbar("value")+1);
                
                if(status == "success"){
                    $.notify("Archivo cargado correctamente ",
                        {
                            position:"top center",
                            className:"success"
                        }
                    );
                    $thumb.hide("slow",function(){ $thumb.remove(); });
                }else{
                    $.notify("Error al cargar ",
                        {
                            position:"top center",
                            className:"error"
                        }
                    );
                }
        },
        type: 'post'

    });
}

function resizeImg(img){
    console.log('resize',arguments);

    //crop, image must fill the desaired area
    var width = 1300,
        height = 900,
        ratio = width/height,
        sx,sy,sw,sh;

    //get actual ratio
    var aRatio = img.width/img.height;
    console.log(aRatio);

    switch(true){
        case ratio == aRatio:
            sx = 0;
            sy = 0;
            sw = width;
            sh = height;
            break;
        case (ratio > 1 && aRatio > 1) || (ratio < 1 && aRatio > 1):
            console.log('horizontales');
            sw = height*aRatio;
            sh = height;

            sx = -(sw-width)/2;
            sy = 0;

            if(sw >= width)
                break;
        case (ratio > 1 && aRatio < 1) || (ratio < 1 && aRatio < 1):
            sw = width;
            sh = width/aRatio;

            sx = 0;
            sy = -(sh-height)/2;
            break;
    }

    var c = document.createElement("canvas");

    c.width = width;
    c.height = height;

    var ctx = c.getContext("2d");
    console.log(img,sx,sy,sw,sh,width,height);
    //ctx.drawImage(this,0,0,sw,sh,sx,sy,width,height);
    ctx.drawImage(img,sx,sy,sw,sh);

    //$("body")[0].appendChild(c);

    return c;
}
function addLogo(canvas,logo,pos,size){
    if(typeof logo == "string"){
        var url = logo;
        logo = document.createElement("img");
        logo.src = url;
        logo.ratio = logo.width/logo.height;
    }

    if(size !== undefined){
        if(size.width !== undefined){
            logo.width = size.width;
            if(size.height === undefined)
                size.height = size.width/logo.ratio;
        }
        if(size.height !== undefined){
            logo.height = size.height;
            if(size.width === undefined)
                size.width = logo.height*logo.ratio;
        }
    }else{
        size = {
            width: logo.width,
            height: logo.height
        };
    }

    if(pos.x < 0 ){
        pos.x = canvas.width - logo.width + pos.x;
    }
    if(pos.y < 0){
        pos.y = canvas.height - logo.height + pos.y;
    }

    var ctx = canvas.getContext("2d");
    ctx.globalAlpha = 0.75;
    ctx.drawImage(logo,pos.x,pos.y,size.width,size.height);
}

var test = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAA3NCSVQICAjb4U/gAAACZ1BMVEX///+vpaFhv9xiudnSmInUlINlwOBkvt5kw+OSpqycoaWdoKJnw+Vkw+Ngvt9atc5bs8hnw+Vgvt+mmpdhv9x5zutyyuhjwdxhv9xZudNWt9BQttVvqruZmZn////5/P3y/P3q+/zp9/rf+fzd+PvX+Pzk9fjf9PfP9/zS9fvP9fvS8/rX8PfM9PvD9P3C9P/S8PfG8vvQ7vS+8/658v3O7fPD7/m/8Pu87/q08Py07/rE6vOu7vyq7/ym7vy/6PGp6/us6vm55vKf6/yx5/Sp6Pen5/iz5PGh6fui5/md5/us4++Z5v6h4/Ob5feS5/yX5fid4vOO5PmK5fyg3fCf3fGG4vmb3O2S3vKF3/mV2fCB3viP2+583viB3fiS1/B43viH2/F+2/J02/aK1exy2/d82PKC1/OG1up42PKG0+uI0+tl1/h/0ul/0Oxu0+960OV10ul+z+h5zutn0/Fc1fdyzuh2zelk0e9g0OxyyuhtzOZ0yuhuyutry+Rqyutpyudly+dlyut3xt5pyeNxxud0xdxlx+hMzvFpxt9yw9phx+Fdx+dhxuZjxd9Ly+5wwthXyOdNy+xQyetdxuNSyehaxeJIye1TxuVtwNdMxudjwdxGx+ppv9hIxudWwuRQw+FPwuJNwuFDw+VIwuJlutdFwuNBwuNKv99Mv+FEv+JCv+JHvd1ft89ZudNBvuA9vd9Hu9tFu9tWt9A8u91JuNdCuttQttU3u945uttIttM/uNlGtdE3t9s1t9owttpAs9E0tddCsc08sc+ZmZkxs9Y+r805sM4ssdYqsdQ7rcwrr9c2qskcnpCoAAAAzXRSTlMAM0RERERVVXd3d3eIiLu7u8zMzN3u7u7u7u7u7u7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////+fUuggAAAAlwSFlzAAALEgAACxIB0t1+/AAAAB50RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNS4xqx9I6wAAAsVJREFUOI2t02lXElEcgHFtt2wxtxa7qIRiISBqZUibmWalpUSh4RaQYbm0TAkoSgZpM7hgLpOhqIGiBhoZuGBUFJUfqtmg5VUves5hhjn/37nnDocbEvJ/2hzxVKlWKtVqNXaD8K/YJ2JTcB4eXVFa8XelFdHh1HxLnVDRjKXRapp/SyGs207M18cIlbBei8D36yBY+ysEuhqzDgc7FApYr4cfcQGIP6PVajQaEuhhhWI3DmKlEILAqkQQnwAAP7iGXo9A0lgcRNWq+pCRPJDAoDNooBYmp1hIs/QlDvYoWmF4hAUYdDqDDoRmGHsa0SMI0qepIUAUBVKYTCYdJHJZLBa/tB9GYLiVXGH/PRIw2Wz2QUDFx0Xr3fc4WDPMLU2ZWSCVw+akMRlYTGwv18bMC3OGNRx8Nyz98H3LBOyMdCwOh5OenpEGeH6ff6njKw6+GBZ8Xn8myBBkBxJkgWSv17fQToKOeZ9nlQeyTggEJ3LIUsFJ/4p3vv0TDj62z3hXMJCdk59zPAXvELbX5x7nykwQeNweHjh1saAojXqJ5IZVp9M92fKBBJNuJwbyi4tKjpLzpIZVu93hnlQRYLkFAy4uKCwpFh0L/A4XXNN255hqmQRTToeLBy6XiUTFBXiFp+NBjcvmoMCiasxhd+WBXJlEIqkkkhUCrsNmN0OLAWBzNIJEmaySSiYBSRbL9Cj0jgSjdpvFygOHb96henge8K0m2yAFoEG7xTQ+mAxoR3LPEWF7bbSaLAGganNMmNDhF2dpgXcAtBvjKGptI8GufumEBUWNxuGmqiuXiK43DRtRE1oF78RB6L56aY/JhBp7e7u6nmF1DQz0Go1oj7R+byjxvw97+/iW/LZcXl0uFouwxOKy8mp5tfjBmzDq5GyLm309NNTdqdM9IdLpOru7X83GbQ2evQ2Rn//oAH6J3PhP5/on/FlVMstPiUYAAAAASUVORK5CYII=";

document.getElementById('test').src=test;
</script>
</body>
</html>