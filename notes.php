<?php header("Content-Type: image/svg+xml"); 
print "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>";
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg version="1.1"   xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<script type="text/javascript">
<![CDATA[
//this will create htmljavascriptfunctionname in html document and link it to changeText
top.htmljavascriptfunctionname = changeText;
 
function changeText(txt){
   targetText=document.getElementById("thetext");
   var newText = document.createTextNode(txt);
   targetText.replaceChild(newText,targetText.childNodes[0]);
}
// ]]>
</script>

<?php

$NOTES = $_GET['notes'];

if ( $NOTES == 'Notes'){
	print "<text id=\"thetext\" transform=\"rotate(270,20,40)\" font-size=\"14\" font-weight=\"bold\" x=\"20\" y=\"40\" >".$NOTES."</text></svg>";
}
else {
	print "<text id=\"thetext\" transform=\"rotate(270,12,60)\" font-size=\"10\" x=\"12\" y=\"60\" >".$NOTES."</text></svg>";
}
 ?>

