var JSFL_PATH = fl.scriptURI.substr(0,fl.scriptURI.lastIndexOf("/") + 1);
var str ="demo" 
var fla = JSFL_PATH + str + ".fla"; 
fl.openDocument(fla);

var doc=fl.getDocumentDOM();
doc.importFile(JSFL_PATH + "com/mp3/" + "577292f2de7e6.mp3", true);

var tl=doc.getTimeline();
var lib=doc.library; 
var li;

doc.docClass="Main"; 
 
li = lib.items[lib.findItemIndex("577292f2de7e6.mp3")];
li.linkageExportForAS = true;
li.linkageExportForRS = false;
li.linkageExportInFirstFrame = true;
li.linkageClassName = "mp3class"; 

fl.saveDocument(doc,JSFL_PATH + "《陶泥的世界》美术一点通.fla");
doc.exportSWF(JSFL_PATH + "《陶泥的世界》美术一点通.fla");
fl.closeDocument(JSFL_PATH + "《陶泥的世界》美术一点通.fla");