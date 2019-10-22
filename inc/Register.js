window.onload = function(){
var id=2;
function addFields(){

	if (id <= 3) {

	  // Number of inputs to create
	  // Container <div> where dynamic content will be placed
	  var container = document.getElementById("degreeDiv");
	  var degreeContainer = document.getElementById("degree1");
	  // Clear previous contents of the container
	  var clone = degreeContainer.cloneNode(true);
	  clone.setAttribute("id", "degree"+id);
	  var btn = clone.getElementsByTagName("button")[0];
	  btn.id = "addDegreeButton" + id;
	  var sel = clone.getElementsByTagName("select")[0];
	  sel.name = "vDegree" + id;
	  var school = clone.getElementsByTagName("input")[0];
	  school.name = "vSchool" + id;
	  school.value = "";
	  var major = clone.getElementsByTagName("input")[1];
	  major.name = "vMajor" + id;
	  major.value = "";
	  var gradYear = clone.getElementsByTagName("select")[1];
	  gradYear.name = "vGradYear" + id;
		  // Append a node with a random text
		  container.appendChild(clone);
		  // Create an <input> element, set its type and name attributes
		  // Append a line break
		  container.appendChild(document.createElement("br"));
		  id += 1;

	} else {
	  alert("sorry, maximum of 3 degrees allowed to be entered");
	}



}


var buttonlistener = document.getElementsByClassName("addDeg");

document.addEventListener('click', function(e){
  if(e.target.className=="addDeg"){
   addFields();
  }
})

}
