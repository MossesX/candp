<?php require_once 'quickmysql.php' ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Colors</title>
        <meta charset="UTF-8">
        <meta content="width=device-width,initial-scale=1.0" name="viewport"/> 
        <meta name="description" content="Graphic design and web development">
        <meta name="author" content="Kyle Hamilton">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>
            $(function(){
                var container = $("#container"), color_li = $('.color');
                
                container.on('mouseenter', '.color', function(){
                    var that = $(this), color = that.text();
                    that.css({"background-color":color});
                });
                container.on('mouseleave', '.color', function(){
                    $(this).css({"background-color":"white"});
                });
                color_li.one('click', function(){
                    var id = $(this).text();
                    getColors(id);
                });
                $("#total").on("click", function(){
                    $(this).next().html(format(votesTot.sum().toString()));
                });
                
                //var isEmpty = function(obj) {return Object.keys(obj).length === 0;} // keys not available in IE < 9
                var isEmpty = function(obj){
                    var r = 0;
                    for (var p in obj)if(obj.hasOwnProperty( p ))r++;
                        if(r == 0)return true;
                    }
                
                var dataObj = {}, votesTot = [];
            
                var getColors = (function(id){
                    
                    function writeToDOM(){
                        var colorName = dataObj.colors[id], numVotes = 0;
                        for(var i = 0; i < colorName.length; i++){
                            if(colorName[i].votes){
                                numVotes += parseInt(colorName[i].votes);
                            }
                        }
                        $(".color:contains("+id+")").next().text(format(numVotes.toString()));
                        votesTot.push(numVotes);
                    }
                    // cache the data - if data changes frequently I may not want to do this //
                    if(!isEmpty(dataObj)){
                        writeToDOM();                
                    }else{
                        $.ajax({
                            url: 'colors.json',
                            dataType: 'jsonp',
                            jsonp: false,
                            jsonpCallback: 'colors',
                            beforeSend: function(){},
                                
                            error: function(jqXHR, exception) {
                                if (jqXHR.status === 0) {
                                    console.log('Not connect.\n Verify Network.');
                                } else if (jqXHR.status == 404) {
                                    console.log('Requested page not found. [404]');
                                } else if (jqXHR.status == 500) {
                                    console.log('Internal Server Error [500].');
                                } else if (exception === 'parsererror') {
                                    console.log('Requested JSON parse failed.');
                                } else if (exception === 'timeout') {
                                    console.log('Time out error.');
                                } else if (exception === 'abort') {
                                    console.log('Ajax request aborted.');
                                } else {
                                    console.log('Uncaught Error.\n' + jqXHR.responseText);
                                }
                            },
                                
                            success: function(data){
                                dataObj = data;
                                writeToDOM();
                            }
                        });
                    }
                 });
            });
            Array.prototype.sum = function(){
                for(var i=0,sum=0;i<this.length;sum+=this[i++]);
                return sum;
            }
            function format(string){   
                string = string.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                return string;
            }    
        </script>
        <style>
            #container, #container li{
                list-style: none;
                font-family: helvetica;
                color:#363636;
                margin:0;
                padding:0;
            }
            #container{
                border: 2px solid #CCC;
                position: relative;
                overflow: hidden;
                width:100%;
                max-width: 394px;
                margin:0 auto;
            }
            #container li{
                float:left;
                border:1px solid #ccc;
                padding:5px 10px;
                height:24px;
                line-height: 24px;
                width:43%;
                cursor:pointer;
            }
            .color, #total{  clear:left; 
        -webkit-transition: background-color 0.3s ease;
           -moz-transition: background-color 0.3s ease;
             -o-transition: background-color 0.3s ease;
            -ms-transition: background-color 0.3s ease;
                transition: background-color 0.3s ease;
            }
            #total, .totalNum{ font-weight: bold; }
            .color:hover, #total:hover{ text-decoration: underline; }
            #container .color.complement:hover{ color:white; }
            p{ text-align: center; }
        </style>
    </head>
    <body>
        <ul id="container">
            
            <?php
            connect();
            $query = "SELECT *  FROM  `colors`";  
            $result = mysql_query($query) or die(mysql_error());
            $rows = mysql_num_rows($result);
            if ($rows > 0) {
                        while ($data = mysql_fetch_assoc($result)) {
                            $color = $data['color'];
                            echo '<li class="color">'.$color.'</li><li></li>';
                        }
                    }
            disconnect();
            ?>
            <li id="total">Total</li><li class="totalNum"></li>
        </ul>
        <p><a href="#" onclick="location.reload();" >Refresh</a></p>
    </body>
</html>


