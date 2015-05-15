var tab = [[1,2],[3,4]];
var obj = {name:"a",tab:[2,4]};

function deep_clone_array(original){
	var copy = [];
	original.forEach(function(element){
		if (typeof element == "object") {
			// If it's an array, reapply deep_clone_array on it, else deep_clone_object.
			if (element.length != null) {
				copy.push(deep_clone_array(element));
			}
			else {
				copy.push(deep_clone_object(element));
			}
		}
		else if (typeof element == "string" || typeof element == "number") {
			copy.push(element);
		}
	});	
	return copy;
}

function deep_clone_object(original) {
	var copy = {};
	for (var property in original) {
		var element = eval("original." + property);
		if (typeof element == "object"){
			// If it's an array, reapply deep_clone_array on it, else deep_clone_object.
			if (element.length != null) {
				eval("copy." + property + "= deep_clone_array(element)");
			}
			else {
				eval("copy." + property + "= deep_clone_object(element)");
			}
		}
		else if (typeof element == "string" || typeof element == "number") {
			eval("copy." + property + "= element");
		}	
	}
	return copy;
}