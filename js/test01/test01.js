<!DOCTYPE html>
    <html>
    <head>
    <title>Exemple DOM</title>
    <script>
    function testerRadio() 
    {
        var radio = document.getElementsByName("btnRadChoix");
                for (var i=0; i<radio.length;i++) 
                {
                    if (radio[i].checked) 
                    {
                        document.getElementById("txtBox1").value = radio[i].value;
                    }
                }
            }
        </script>
    </head>
    <body>
        <form name="frmChoix">
            <input type="radio" name="btnRadChoix" value="semaine" checked> Semaine
            <br>
            <input type="radio"  name="btnRadChoix" value="week end"> Week-end 
            <br>
            <input type="button" name="btn" value="Choix" onclick="testerRadio();">
            <br>
            <input id="txtBox1" type ="text" name="txtChoix" value="">
        </form>
    </body>
    </html>



   
  
