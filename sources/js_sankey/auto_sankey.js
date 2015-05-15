// For every link, recover node id from node name
var nodes_names = [];
numerotate_nodes();

// For every link, add id numerotation (necessary in add_links() function)
numerotate_links();

var alerte = false,
	message = '';

links.forEach(function(link){
	link.source = nodes_names.indexOf(link.source_name);
	if (link.source == -1) {
		alerte = true;
		message += "link" + link.id + ": no match for source name '" + link.source_name + "'\n";
	}
	link.target = nodes_names.indexOf(link.target_name);
	if (link.target == -1) {
		alerte = true;
		message += "link" + link.id + ": no match for target name '" + link.target_name + "'\n";
	}
});

if (alerte) {
	alert(message);
}

// Compute nodes
compute_nodes();

// Use a relevant scale
var max_node_value = 0;
nodes.forEach(function(node){
	if (node.total_input_offset > max_node_value) {
		max_node_value = node.total_input_offset;
	}
	if (node.total_output_offset > max_node_value) {
		max_node_value = node.total_output_offset;
	}
});
document.scale_info.scale.value = max_node_value;
scale.domain([0,max_node_value]);


// Horizontal position of vertical nodes
var max_horizontal_index = 0;

nodes.forEach(function(node){
	if (node.orientation == "vertical") {
//		console.log(node.id);
		node.horizontal_index = explore_branch(node.id, 0);
		if (node.horizontal_index > max_horizontal_index) {
			max_horizontal_index = node.horizontal_index;
		}
	}
});

function explore_branch(node_id, current_length) {
//	console.log(node_id + " / " + current_length);
	var no_input_link = true;
	var highest_branch_length = current_length;
	links.forEach(function(link) {
		if (link.target == node_id) {
			no_input_link = false;
			branch_length = explore_branch(link.source, current_length + 1);
			if (branch_length > highest_branch_length) {
				highest_branch_length = branch_length;
			}
		}
	});
	if (no_input_link == true) {
		return current_length;
	}
	else {
		return highest_branch_length;
	}
};

nodes.forEach(function(node){
	if (node.orientation == "vertical") {
		node.x = 0.05 * width + node.horizontal_index / max_horizontal_index * width * 0.9;
	}
});
	
// Vertical position of vertical nodes
	// compute total height of nodes that belong to the same column, then compute the spaces between them and their positions.
for (var i = 0; i <= max_horizontal_index; i++) {
	var total_height = 0,
		total_nb = 0,
		vertical_space,
		vertical_offset = 0;
	var the_nodes = nodes.filter(function(node){
			return node.horizontal_index == i;
		});	
	the_nodes.forEach(function(node){
		total_height += scale(Math.max(node.total_input_offset, node.total_output_offset));
		total_nb += 1;
	}); 
	if (total_nb > 1) {
		vertical_space = (0.6 * height - total_height) / (total_nb - 1);
	}
	else {
		vertical_space = 0;
	}
	the_nodes.forEach(function(node,id){
		if (id == 0) {
			node.y = 0.2 * height;
			vertical_offset = 0.2 * height + scale(Math.max(node.total_input_offset, node.total_output_offset)) + vertical_space;
		}
		else {
			node.y = vertical_offset;
			vertical_offset += scale(Math.max(node.total_input_offset, node.total_output_offset)) + vertical_space;
		}
	});
}
		
// Vertical position of horizontal nodes
nodes.forEach(function(node){
	if (node.orientation == "horizontal") {
		if (explore_branch(node.id,0) == 0) {
			node.y = 0.05 * height;
		}
		else {
			node.y = 0.95 * height;
		}
	}
});

// Horizontal position of horizontal nodes

	// associate position constraint to each horizontal node and order nodes by constraints
var list_of_x_before = [],
	list_of_x_after = [],
	the_nodes_min = [],
	the_nodes_max = [];
		
nodes.forEach(function(node){
	if (node.orientation == "horizontal") {
		if (node.output_links.length > 0) {
			var min_x = width;
			node.output_links.forEach(function(link){
				if (nodes[links[link].target].x < min_x) {
					min_x = nodes[links[link].target].x;
				}
			});
			node.x_before = min_x;
			if (list_of_x_before.indexOf(min_x) == -1) {
				list_of_x_before.push(min_x);
				the_nodes_min[min_x] = [];
			}
			the_nodes_min[min_x].push(node);
		}		
		else if (node.input_links.length > 0) {
			var max_x = 0;
			node.input_links.forEach(function(link){
				if (nodes[links[link].source].x > max_x) {
					max_x = nodes[links[link].source].x;
				}
			});
			node.x_after = max_x;
			if (list_of_x_after.indexOf(max_x) == -1) {
				list_of_x_after.push(max_x);
				the_nodes_max[max_x] = [];
			}
			the_nodes_max[max_x].push(node);
		}
	}
});

	// give x position to horiz nodes
list_of_x_before.forEach(function(x_before){
	var horizontal_offset = x_before - 3 * default_node_size;
	the_nodes_min[x_before].forEach(function(node){
		node.x = horizontal_offset - scale(node.total_output_offset);
		horizontal_offset -= scale(node.total_output_offset) + 3 * default_node_size;
	});
});	

list_of_x_after.forEach(function(x_after){
	var horizontal_offset = x_after + 3 * default_node_size;
	the_nodes_max[x_after].forEach(function(node){
		node.x = horizontal_offset;
		horizontal_offset += scale(node.total_input_offset) + 3 * default_node_size;
	});
});	
		
// DRAW SANKEY	

add_nodes_auto();
add_links();

// put nodes forward (links in the back)
move_nodes_forward();



var max_link_value = 0
links.forEach(function(link){
	if (link.value > max_link_value) {
		max_link_value = link.value;
	}
});
document.getElementById("filter_id").max = max_link_value;


