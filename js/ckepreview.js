document.addEventListener("DOMContentLoaded", function (f){

if("undefined"!=typeof CKEDITOR&&null!=CKEDITOR)
{
CKEDITOR.on("instanceLoaded",
function(e){
var m=document.createElement("div");
m.setAttribute("id","muffin-prev"),
document.querySelector(".cke").
parentNode.appendChild(m);
e.editor.on("change",function(){document.getElementById("muffin-prev").innerHTML=e.editor.getData();
MathJax.Hub.Queue(['Typeset', MathJax.Hub, "muffin-prev"]);
prettyPrint();
}
);
m.innerHTML=e.editor.getData(true);
MathJax.Hub.Queue(['Typeset', MathJax.Hub, "muffin-prev"]);
prettyPrint();
});
}
});

