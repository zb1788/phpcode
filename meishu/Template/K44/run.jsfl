var JSFL_PATH = fl.scriptURI.substr(0,fl.scriptURI.lastIndexOf("/") + 1);
var str ="demo" 
var fla = JSFL_PATH + str + ".fla"; 
fl.openDocument(fla);

var doc=fl.getDocumentDOM();
doc.importFile(JSFL_PATH + "com/mp3/" + "$$mp3$$", true);

var tl=doc.getTimeline();
var lib=doc.library; 
var li;

doc.docClass="Main"; 
 
li = lib.items[lib.findItemIndex("$$mp3$$")];
li.linkageExportForAS = true;
li.linkageExportForRS = false;
li.linkageExportInFirstFrame = true;
li.linkageClassName = "mp3class"; 

fl.saveDocument(doc,JSFL_PATH + "$$kecheng$$.fla");
doc.exportSWF(JSFL_PATH + "$$kecheng$$.fla");
fl.closeDocument(JSFL_PATH + "$$kecheng$$.fla");