var JSFL_PATH = fl.scriptURI.substr(0,fl.scriptURI.lastIndexOf("/") + 1);
var str ="demo" 
var fla = JSFL_PATH + str + ".fla"; 
fl.openDocument(fla);

var doc=fl.getDocumentDOM();


var tl=doc.getTimeline();
var lib=doc.library; 
var li;

doc.docClass="Main"; 
 

fl.saveDocument(doc,JSFL_PATH + "sdfs11122.fla");
doc.exportSWF(JSFL_PATH + "sdfs11122.fla");
fl.closeDocument(JSFL_PATH + "sdfs11122.fla");