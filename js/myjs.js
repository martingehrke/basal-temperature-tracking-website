function do_something(){
                $(this).toggleClass("ui-selected").siblings().removeClass("ui-selected");
                if ( $(this).attr("class") == "ui-selected" ){
                        $("#info").load("./updatedb.php?action=add&temp=" + this.value + "&day=" + $(this).parent().attr("id")); }
                else {
                        $("#info").load("./updatedb.php?action=remove&temp=" + this.value + "&day=" + $(this).parent().attr("id")); }
        }
