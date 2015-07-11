// SIZES AND CURVATURES
var width = 2000,
    height = 1500,
    default_node_size = 10,
    default_horiz_shift = 50,
    // curvature_1 is applied to vert/vert links, curvature_2 is applied to horiz/vert and vert/horiz links
    curvature_1 = 1,
    curvature_2 = 1;

// DATA
var nodes = [],
	links = [];
	
// SCALE
var scale = d3.scale.linear()
		.domain([0,100])
		.range([0,100]);

function update_scale() {
	var user_scale = document.scale_info.scale.value;
	scale.domain([0,user_scale]);
	redraw_nodes_and_links();
} 

function update_smart_scale() {
	var user_scale = document.smart_scale_info.smart_scale.value;
	scale.domain([0,user_scale]);
	redraw_nodes_and_links();
	smart_scale_reposition();
} 

// D3 APPEND SVG AND MAIN GROUPS
var svg = d3.select("body").append("svg")
   	.attr("width", width)
   	.attr("height", height)
   	.attr("id","svg");

var g_links = svg.append("g").attr("class", "g_links").attr("id","g_links"),
	g_nodes = svg.append("g").attr("class", "g_nodes").attr("id","g_nodes");
	
	
// CURRENT SELECTION
var selected_item = "";		

function deselect_nodes_and_links() {
	g_nodes.selectAll("rect").attr("class","node");
	g_links.selectAll("path").attr("class","link");
	selected_item = "";
}
	
// ADD NEW NODE : in the middle == true if done manually.    
function add_new_node() {
	if ($('input[name="orientation"]:checked').val() == undefined) {
		alert('Select orientation');
		return;
	}
	var name = document.node_info.name.value,
		orientation = $('input[name="orientation"]:checked').val(),
		color = document.node_info.color.value,
		id = nodes.length;
	
	var x = width/3 - default_node_size/3,
   		y = height/3 - default_node_size/3;	
	
	nodes.push( { "id":id, "name":name,"orientation":orientation,"color":color, "input_links":[], "input_offsets":[0], "total_input_offset":0, "output_links":[], "output_offsets":[0], "total_output_offset":0, "x":x, "y":y, "merged_name":"idem" } );
   	
   	// attr x and y have no meaning for <g>, they are just used to store the data.	

	add_nodes_auto();    	
}
   
// ADD NEW LINK   
function add_new_link() {
	var source = parseInt(document.link_info.source.value),
		target = parseInt(document.link_info.target.value),
		value = parseFloat(document.link_info.value.value),
		color = document.link_info.color.value;
	
	var id = links.length;
	links.push({"id":id, "source": source, "target": target, "source_name": nodes[source].name, "target_name": nodes[target].name,"value": value, "color": color});	
	
    nodes[source].output_links.push(id);
    nodes[target].input_links.push(id);
    nodes[source].total_output_offset += parseFloat(value); 
    nodes[target].total_input_offset += parseFloat(value); 
    nodes[source].output_offsets.push(nodes[source].total_output_offset);
	nodes[target].input_offsets.push(nodes[target].total_input_offset);
	
	// upadte filter range.
	if (document.getElementById("filter_id").max < value) {
		document.getElementById("filter_id").max = value;
	}
	
	add_links();
}

var z_order_max = 0;

function add_links() {
	var gg_links = g_links.selectAll(".gg_links").data(links).enter().append("g")
		.attr("id",function(d,i){
			return "gg_link" + i;
		})
		.attr("class","gg_links")
		.on("click", function(d){
	     	// put link in front
	     	document.getElementById("g_links").appendChild(document.getElementById("gg_link" + d.id));
	    });
		
	var paths = gg_links.append("path")
		.attr("fill","none")
		.attr("class", "link")
		.attr("id", function(d, i) {
    		return "link" + i;
    	})
     	.attr("stroke-width", function(d) {
     		return scale(d.value);
     	})
	    .attr("stroke",function(d){
	    	return d.color;
	    })
	    .on("click", function(d,i){
	     	deselect_nodes_and_links();
	     	d3.select(this).attr("class","selected_link");
	     	selected_item = "link" + i;
	     	document.link_info.source.value = d.source;
	     	document.link_info.target.value = d.target;
	     	document.link_info.value.value = d.value;
	     	document.link_info.color.value = d.color;
	    })
	    .call(d3.behavior.drag()
   			.origin(Object)
			.on("drag", function() {
				drag_link(this);
			})
		);
		
	// link value
	 gg_links.append("text")
	 	.attr("id",function(d,i){
 			return "link_value" + i;
 		})
	 	.attr("style","font-family:Arial; font-size:11px;")
	 	.call(d3.behavior.drag()
   			.origin(Object).on("drag", function() {
   				if (alt_key_pressed == true) {
					drag_text(this);
   				}
   				else {
   					var link_to_drag = "link" + d3.select(this).attr("id").substring(4);
					drag_link(document.getElementById(link_to_drag));
				}
			})
		); 
		
	// link sd_value
	gg_links.append("text")
	 	.attr("id",function(d,i){
 			return "link_sd_value" + i;
 		})
 		.each(function(d,i){
 			if (typeof d.x_sd_label == "undefined" && typeof d.x_label != "undefined"){
 				d.x_sd_label = d.x_label;
 			}
 			if (typeof d.y_sd_label == "undefined" && typeof d.y_label != "undefined"){
 				d.y_sd_label = parseFloat(d.y_label + 10);
 			}
 		})
	 	.attr("style","font-family:Arial; font-size:9px;")
	 	.call(d3.behavior.drag()
   			.origin(Object).on("drag", function() {
   				if (alt_key_pressed == true) {
					drag_text(this);
   				}
   				else {
   					var link_to_drag = "link" + d3.select(this).attr("id").substring(4);
					drag_link(document.getElementById(link_to_drag));
				}
			})
		); 	
	
	paths.attr("d", function(d) {
     	return drawCurve(d);
     })
}
   
// DRAG NODES
function drag_node(dragged) {
	var old_x = +d3.select(dragged).attr("x"),
		old_y = +d3.select(dragged).attr("y"),
		new_x = old_x + d3.event.dx,
    	new_y = old_y + d3.event.dy;
    	
    if (new_x < 0 || new_x > (width - default_node_size) || new_y < 0 || new_y > (height - default_node_size)) {
    	return;
    }

    d3.select(dragged).attr("transform","translate(" + new_x + "," + new_y + ")")
    	.attr("x",new_x)
    	.attr("y",new_y);
    
    var id = d3.select(dragged).attr("id");
    nodes[id.substring(7)].x = new_x;
    nodes[id.substring(7)].y = new_y;
    
    links.forEach(function(link,i){
        if (link.source == dragged.id.match(/\d+/)[0] || link.target == dragged.id.match(/\d+/)[0]) {
            // Redraw link
            d3.select('#link' + i).attr("d", function(d) {
                return drawCurve(d);
            });
            var old_x_pos = +d3.select("#link_value" + i).attr("x"),
            	old_y_pos = +d3.select("#link_value" + i).attr("y");
            d3.select("#link_value" + i).attr("x",old_x_pos + 1/2 * (new_x - old_x));
            d3.select("#link_value" + i).attr("y",old_y_pos + 1/2 * (new_y - old_y));
        }
    });       
}

// DRAG LINK
function drag_link(dragged) {
	var id = d3.select(dragged).attr("id").substring(4)
		linked_node = identify_node(id,d3.mouse(dragged));
	if (linked_node == undefined) {
		return;
	}
	node = nodes[linked_node.node_id];
	if (linked_node.type == "source" && node.orientation == "vertical") {
		var link_order = my_index_of(node.output_links,id),
			number_of_links = node.output_links.length,
			current_offset = node.output_offsets[link_order],
			value = links[id].value;
			if (link_order < number_of_links - 1 && d3.mouse(dragged)[1] + d3.event.dy >= linked_node.origin + scale(current_offset + value)) {
				node.output_links.swap(link_order,link_order+1);
				recompute_node(linked_node.node_id);
			}
			if (link_order > 0 && d3.mouse(dragged)[1] + d3.event.dy <= linked_node.origin + scale(current_offset)) {
				node.output_links.swap(link_order,link_order-1);
				recompute_node(linked_node.node_id);
			}  
	}
	if (linked_node.type == "target" && node.orientation == "vertical") {
		var link_order = my_index_of(node.input_links,id),
			number_of_links = node.input_links.length,
			current_offset = node.input_offsets[link_order],
			value = links[id].value;
			if (link_order < number_of_links - 1 && d3.mouse(dragged)[1] + d3.event.dy >= linked_node.origin + scale(current_offset + value)) {
				node.input_links.swap(link_order,link_order+1);
				recompute_node(linked_node.node_id);
			} 
			if (link_order > 0 && d3.mouse(dragged)[1] + d3.event.dy <= linked_node.origin + scale(current_offset)) {
				node.input_links.swap(link_order,link_order-1);
				recompute_node(linked_node.node_id);
			}  
	}
	if (linked_node.type == "source" && node.orientation == "horizontal") {
		var link_order = my_index_of(node.output_links,id),
			number_of_links = node.output_links.length,
			current_offset = node.output_offsets[link_order],
			value = links[id].value;
			if (link_order < number_of_links - 1 && d3.mouse(dragged)[0] + d3.event.dx >= linked_node.origin + scale(current_offset + value)) {
				node.output_links.swap(link_order,link_order+1);
				recompute_node(linked_node.node_id);
			}
			if (link_order > 0 && d3.mouse(dragged)[0] + d3.event.dx <= linked_node.origin + scale(current_offset)) {
				node.output_links.swap(link_order,link_order-1);
				recompute_node(linked_node.node_id);
			}  
	}
	if (linked_node.type == "target" && node.orientation == "horizontal") {
		var link_order = my_index_of(node.input_links,id),
			number_of_links = node.input_links.length,
			current_offset = node.input_offsets[link_order],
			value = links[id].value;
			if (link_order < number_of_links - 1 && d3.mouse(dragged)[0] + d3.event.dx >= linked_node.origin + scale(current_offset + value)) {
				node.input_links.swap(link_order,link_order+1);
				recompute_node(linked_node.node_id);
			} 
			if (link_order > 0 && d3.mouse(dragged)[0] + d3.event.dx <= linked_node.origin + scale(current_offset)) {
				node.input_links.swap(link_order,link_order-1);
				recompute_node(linked_node.node_id);
			}  
	}
}

// Drag link handle
function drag_handle(dragged,handle_type) {
	var old_x = +d3.select(dragged).attr("transform").split(",")[0].substring(10),
		old_y_str = d3.select(dragged).attr("transform").split(",")[1];
	var old_y = +old_y_str.substring(0,old_y_str.length-1),
		new_x = old_x + d3.event.dx,
		new_y = old_y + d3.event.dy,
		d = d3.select(dragged).data()[0];
	if (handle_type == "center"){
	    var link_x_length = Math.abs(nodes[d.target].x - nodes[d.source].x - default_node_size);
	    var x_center_new = (new_x - nodes[d.source].x - default_node_size)  / link_x_length;
	    if (x_center_new >= 1/7 && x_center_new <= 6/7) {
	    	d.x_center = x_center_new;
		    links[d.id] = d; // Update data then update viz
			d3.select("#link" + d.id).attr("d",function(){
		    	return drawCurve(d);
		    });
	    }
	}
	else if (handle_type == "vert"){
		if (d.vert_shift + d3.event.dy > -0.5 * scale(d.value) && new_y < height - scale(d.value)/2) {
			d.vert_shift += d3.event.dy;
			links[d.id] = d; // Update data then update viz
			d3.select("#link" + d.id).attr("d",function(){
		    	return drawCurve(d);
		    });
		}
	}
	else if (handle_type == "left"){
		if (d.left_horiz_shift + d3.event.dx < default_horiz_shift && new_x > scale(d.value)/2) {
			d.left_horiz_shift += d3.event.dx;
			links[d.id] = d; // Update data then update viz
			d3.select("#link" + d.id).attr("d",function(){
		    	return drawCurve(d);
		    });
		}
	}
	else if (handle_type == "right"){
		if (d.right_horiz_shift + d3.event.dx > -default_horiz_shift && new_x < width - scale(d.value)/2) {
			d.right_horiz_shift += d3.event.dx;
			links[d.id] = d; // Update data then update viz
			d3.select("#link" + d.id).attr("d",function(){
		    	return drawCurve(d);
		    });
		}
	}
}

// Identify the node that is the closest from mouse click (either source or target).
function identify_node(link_id,mouse_coord) {
	var source = nodes[links[link_id].source],
		target = nodes[links[link_id].target],
		source_x_min = parseInt(d3.select("#gg_node" + links[link_id].source).attr("x")),
		source_x_max = source_x_min + parseInt(d3.select("#node" + links[link_id].source).attr("width")),
		source_y_min = parseInt(d3.select("#gg_node" + links[link_id].source).attr("y")),
		source_y_max = source_y_min + parseInt(d3.select("#node" + links[link_id].source).attr("height")),
		target_x_min = parseInt(d3.select("#gg_node" + links[link_id].target).attr("x")),
		target_x_max = target_x_min + parseInt(d3.select("#node" + links[link_id].target).attr("width")),
		target_y_min = parseInt(d3.select("#gg_node" + links[link_id].target).attr("y")),
		target_y_max = target_y_min + parseInt(d3.select("#node" + links[link_id].target).attr("height")),
		tolerance = 3 * parseInt(default_node_size);
		
	if (source.orientation == "vertical" && mouse_coord[1] >= source_y_min && mouse_coord[1] <= source_y_max && (mouse_coord[0] <= source_x_max + tolerance)) {
		return {"node_id":links[link_id].source,"type":"source","origin":source_y_min};
	}
	if (target.orientation == "vertical" && mouse_coord[1] >= target_y_min && mouse_coord[1] <= target_y_max && (mouse_coord[0] >= target_x_min - tolerance)) {
		return {"node_id":links[link_id].target,"type":"target","origin":target_y_min};
	}
	if (source.orientation == "horizontal" && mouse_coord[0] >= source_x_min && mouse_coord[0] <= source_x_max && (mouse_coord[1] <= source_y_max + tolerance)) {
		return {"node_id":links[link_id].source,"type":"source","origin":source_x_min};
	}
	if (target.orientation == "horizontal" && mouse_coord[0] >= target_x_min && mouse_coord[0] <= target_x_max && (mouse_coord[1] >= target_y_min - tolerance)) {
		return {"node_id":links[link_id].target,"type":"target","origin":target_x_min};
	}	
}

// DRAW LINK   
function drawCurve(d) {
	var source_orientation = nodes[d.source].orientation,
		target_orientation = nodes[d.target].orientation;		
    var xs = +d3.select('#gg_node' + d.source).attr("x"),
    	ys = +d3.select('#gg_node' + d.source).attr("y"),
    	xt = +d3.select('#gg_node' + d.target).attr("x"),
    	yt = +d3.select('#gg_node' + d.target).attr("y");
    var source_order = nodes[d.source].output_links.indexOf(d.id),
		target_order = nodes[d.target].input_links.indexOf(d.id);
    
    if (source_orientation == 'vertical' && target_orientation == 'vertical' && xt <= xs) { // Recycling
   		xs += default_node_size;
		ys += scale(nodes[d.source].output_offsets[source_order] + d.value/2);	
		yt += scale(nodes[d.target].input_offsets[target_order] + d.value/2);
    	// Draw nodes to the correct height
    	d3.select("#node" + d.source).attr("height",scale(Math.max(nodes[d.source].total_input_offset,nodes[d.source].total_output_offset)));
		d3.select("#node" + d.target).attr("height",scale(Math.max(nodes[d.target].total_input_offset,nodes[d.target].total_output_offset)));
		// Handles
    	if (d3.select("#link_center" + d.id)[0][0] != null){ // if the link_center handle exists, delete it
    		d3.select("#link_center" + d.id).remove();
    	}
    	if (d3.select("#vert_shift" + d.id)[0][0] == null){ // if the left_horiz/right_horiz/vert_hift handles do not exist, create them
    		d3.select("#gg_link" + d.id)
					.append("rect")
					.attr("id",function(d){
		 				return "vert_shift" + d.id;
		 			})
		 			.attr("fill-opacity","0")
		 			.attr("width",default_node_size)
		 			.attr("height",default_node_size)
		 			.on("mouseover", function(){
		 				d3.select(this).attr("fill-opacity","0.7");
		 			})
		 			.on("mouseout", function(){
		 				d3.select(this).attr("fill-opacity","0");
		 			})
		 			.call(d3.behavior.drag()
		   				.origin(Object).on("drag", function() {
							drag_handle(this,"vert");
						})
					);
			d3.select("#gg_link" + d.id)
					.append("rect")
					.attr("id",function(d){
		 				return "left_horiz_shift" + d.id;
		 			})
		 			.attr("fill-opacity","0")
		 			.attr("width",default_node_size)
		 			.attr("height",default_node_size)
		 			.on("mouseover", function(){
		 				d3.select(this).attr("fill-opacity","0.7");
		 			})
		 			.on("mouseout", function(){
		 				d3.select(this).attr("fill-opacity","0");
		 			})
		 			.call(d3.behavior.drag()
		   				.origin(Object).on("drag", function() {
							drag_handle(this,"left");
						})
					);
			d3.select("#gg_link" + d.id)
					.append("rect")
					.attr("id",function(d){
		 				return "right_horiz_shift" + d.id;
		 			})
		 			.attr("fill-opacity","0")
		 			.attr("width",default_node_size)
		 			.attr("height",default_node_size)
		 			.on("mouseover", function(){
		 				d3.select(this).attr("fill-opacity","0.7");
		 			})
		 			.on("mouseout", function(){
		 				d3.select(this).attr("fill-opacity","0");
		 			})
		 			.call(d3.behavior.drag()
		   				.origin(Object).on("drag", function() {
							drag_handle(this,"right");
						})
					);
    	}
    	// Draw handle at the correct position
    	d3.select("#vert_shift" + d.id)
    		.attr("transform",function(d){
    			return handles_positions(d)[0]; // 0 => vertical handle
    		});
    	d3.select("#left_horiz_shift" + d.id)
    		.attr("transform",function(d){
    			return handles_positions(d)[1]; // 1 => left handle
    		});
    	d3.select("#right_horiz_shift" + d.id)
    		.attr("transform",function(d){
    			return handles_positions(d)[2]; // 2 => right handle
    		});
    	// WRITE TEXT (VALUE) NEAR THE VERTICAL HANDLE
		var x_pos = +d3.select("#vert_shift" + d.id).attr("transform").split(",")[0].substring(10),
			y_pos_str = d3.select("#vert_shift" + d.id).attr("transform").split(",")[1];
		var y_pos = +y_pos_str.substring(0,y_pos_str.length-1);
		d3.select("#link_value" + d.id)
		 	.attr("x",function(d){
//		 		if (d.x_label) {
//		 			return d.x_label;
//		 		}
		 		return x_pos + default_node_size;
		 	})
		 	.attr("y",function(d){
//		 		if (d.y_label) {
//		 			return d.y_label;
//		 		}
		 		return y_pos + default_node_size;
		 	})
		 	.text(function(d){
		 		return d.value;
		 	});	
		 // write text (sd_value) below the link value
		 d3.select("#link_sd_value" + d.id)
		 	.attr("x",function(d){
//		 		if (d.x_sd_label) {
//		 			return d.x_sd_label;
//		 		}
//		 		else if (d.x_label) {
//		 			return d.x_label;
//		 		}
		 		return x_pos + default_node_size;
		 	})
		 	.attr("y",function(d){
//		 		if (d.y_sd_label) {
//		 			return d.y_sd_label;
//		 		}
//		 		else if (d.y_label) {
//		 			return parseFloat(d.y_label + 10);
//		 		}
		 		return parseFloat(y_pos + default_node_size + 10);
		 	})
		 	.text(function(d){
		 		if (typeof d.sd_value == "undefined") {
		 			return;
		 		}
		 		var sd_value = parseFloat(2*d.sd_value);
		 		var sd_text = "+/- " + sd_value;
		 		return sd_text;
		 	});	

    	
    	[x0,y0] = [xs,ys];
		[x17,y17] = [xt,yt];
		[x1,y1] = [x0 + default_horiz_shift + d.right_horiz_shift, y0];
		[x16,y16] = [x17 - default_horiz_shift + d.left_horiz_shift, y17];
		[x8,y8] = [x1, Math.max(y0,y17) + scale(2*d.value) + d.vert_shift];
		[x2,y2] = [x1 + scale(d.value), y0]; // controle bézier
		[x3,y3] = [x2,y2]; // controle bézier
		[x4,y4] = [x2,y2 + scale(d.value)];
		[x6,y6] = [x8 + scale(d.value), y8]; // controle bézier
		[x7,y7] = [x6,y6]; // controle bézier
		[x5,y5] = [x2,y6 - scale(d.value)];
		[x9,y9] = [x16,y8];
		[x10,y10] = [x9 - scale(d.value),y9]; // controle bézier
		[x11,y11] = [x10,y10]; // controle bézier
		[x12,y12] = [x10,y10 - scale(d.value)];
		[x14,y14] = [x16 - scale(d.value),y17]; // controle bézier
		[x15,y15] = [x14,y14]; // controle bézier
		[x13,y13] = [x12,y14 + scale(d.value)];
		line1 = 'M ' + x0 + ',' + y0 + ' H ' + x1;
		bezier1 = ' C ' + x2 + ',' + y2 + ' ' + x3 + ',' + y3 + ' ' + x4 + ',' + y4;
		line2 = ' V ' + y5;
		bezier2 = ' C ' + x6 + ',' + y6 + ' ' + x7 + ',' + y7 + ' ' + x8 + ',' + y8;
		line3 = ' H ' + x9;
		bezier3 = ' C ' + x10 + ',' + y10 + ' ' + x11 + ',' + y11 + ' ' + x12 + ',' + y12;
		line4 = ' V ' + y13;
		bezier4 = ' C ' + x14 + ',' + y14 + ' ' + x15 + ',' + y15 + ' ' + x16 + ',' + y16;
		line5 = ' H ' + x17;
    	return line1 + bezier1 + line2 + bezier2 + line3 + bezier3 + line4 + bezier4 + line5;
    }
	else { // Classical cases		
	    // Definition of Bézier curve control points (x2,y2) and (x3,y3).
			// We add or remove a pixel to ensure that the tangents at source
			// and target are oriented correctly.
		var x1, y1, x2, y2, x3, y3, x4, y4;
		if (source_orientation == "vertical" && target_orientation == "vertical") {
			xs += default_node_size;
			ys += scale(nodes[d.source].output_offsets[source_order] + d.value/2);	
			yt += scale(nodes[d.target].input_offsets[target_order] + d.value/2);
			[x0,y0] = [xs,ys];
			[x5,y5] = [xt,yt];
			if (d.x_center) {
				var part1 = (x5 - x0) * (d.x_center - 1/6),
					part3 = (x5 - x0) * (-d.x_center + 5/6);
			}
			else {
				part1 = (x5-x0)/3;
				part3 = (x5-x0)/3;
			}
			x1 = x0 + part1;
			y1 = y0;	
			x4 = x5 - part3;
			y4 = y5;
			x2 = x1 + (x4 - x1) * curvature_1 + 1;
			y2 = y1;
			x3 = x1 + (x4 - x1) * (1 - curvature_1) - 1;
			y3 = y4;
			d3.select("#node" + d.source).attr("height",scale(Math.max(nodes[d.source].total_input_offset,nodes[d.source].total_output_offset)));
			d3.select("#node" + d.target).attr("height",scale(Math.max(nodes[d.target].total_input_offset,nodes[d.target].total_output_offset)));
			
			// link_center	
			if (d3.select("#vert_shift" + d.id)[0][0] != null){ // if the left_horiz/right_horiz/vert_hift handles exist, delete them
				d3.select("#vert_shift" + d.id).remove();
				d3.select("#left_horiz_shift" + d.id).remove();
				d3.select("#right_horiz_shift" + d.id).remove();
			}
			if (d3.select("#link_center" + d.id)[0][0] == null){ // if the link_center handle does not exist, create it
				d3.select("#gg_link" + d.id)
					.append("rect")
					.attr("id",function(d){
		 				return "link_center" + d.id;
		 			})
		 			.attr("fill-opacity","0")
		 			.attr("width",default_node_size)
		 			.attr("height",default_node_size)
		 			.on("mouseover", function(){
		 				d3.select(this).attr("fill-opacity","0.7");
		 			})
		 			.on("mouseout", function(){
		 				d3.select(this).attr("fill-opacity","0");
		 			})
		 			.call(d3.behavior.drag()
		   				.origin(Object).on("drag", function() {
							drag_handle(this,"center");
						})
					);
			}
			// put the handle at the correct position
			d3.select("#link_center" + d.id)
				.attr("transform",function(d){
		 			return handles_positions(d);
		 		})
		}	
		else if (source_orientation == "horizontal" && target_orientation == "vertical") {
			[x0,y0] = [xs,ys];
			[x5,y5] = [xt,yt];
			x0 += scale(nodes[d.source].output_offsets[source_order] + d.value/2);
			y0 += default_node_size;
			y5 += scale(nodes[d.target].input_offsets[target_order] + d.value/2);	
			x1 = x0;
			y1 = y0 + (y5 - y0) * 2 / 3;
			x4 = x5 - (x5 - x0) * 2 / 3;
			y4 = y5;
			x2 = x1;
			y2 = y1 + (y4 - y1) * curvature_2 + 1;
			x3 = x1 + (x4 - x1) * (1 - curvature_2) - 1;
			y3 = y4;
			d3.select("#node" + d.source).attr("width",scale(nodes[d.source].total_output_offset));
			d3.select("#node" + d.target).attr("height",scale(Math.max(nodes[d.target].total_input_offset,nodes[d.target].total_output_offset)));
		}
		else if (source_orientation == "vertical" && target_orientation == "horizontal") {
			[x0,y0] = [xs,ys];
			[x5,y5] = [xt,yt];
			x0 += default_node_size;
			y0 += scale(nodes[d.source].output_offsets[source_order] + d.value/2);
			x5 += scale(nodes[d.target].input_offsets[target_order] + d.value/2);
			x1 = x0 + (x5 - x0) * 2 / 3;
			y1 = y0;
			x4 = x5;
			y4 = y5 - (y5 - y0) * 2 / 3;
			x2 = x1 + (x4 - x1) * curvature_2 + 1;
			y2 = y1;
			x3 = x4;
			y3 = y1 + (y4 - y1) * (1 - curvature_2) - 1;
			d3.select("#node" + d.source).attr("height",scale(Math.max(nodes[d.source].total_input_offset,nodes[d.source].total_output_offset)));
			d3.select("#node" + d.target).attr("width",scale(nodes[d.target].total_input_offset));
		}
		// WRITE TEXT (VALUE) IN THE MIDDLE OF THE LINK
		var t = 0.5,
			x_pos = x0 * Math.pow(1-t,3) + 3*x2 * t * Math.pow(1-t,2) + 3*x3 * Math.pow(t,2) * (1-t) + x1 * Math.pow(t,3),
	 		y_pos = y0 * Math.pow(1-t,3) + 3*y2 * t * Math.pow(1-t,2) + 3*y3 * Math.pow(t,2) * (1-t) + y1 * Math.pow(t,3);  
		d3.select("#link_value" + d.id)
		 	.attr("x",function(d){
//		 		if (d.x_label) {
//		 			return d.x_label;
//		 		}
		 		return x_pos;
		 	})
		 	.attr("y",function(d){
//		 		if (d.y_label) {
//		 			return d.y_label;
//		 		}
		 		return y_pos;
		 	})
		 	.text(function(d){
		 		return d.value;
		 	});	
		 // write text (sd_value) below the link value
		 d3.select("#link_sd_value" + d.id)
		 	.attr("x",function(d){
//		 		if (d.x_sd_label) {
//		 			return d.x_sd_label;
//		 		}
//		 		else if (d.x_label) {
//		 			return d.x_label;
//		 		}
		 		return x_pos;
		 	})
		 	.attr("y",function(d){
//		 		if (d.y_sd_label) {
//		 			return d.y_sd_label;
//		 		}
//		 		else if (d.y_label) {
//		 			return parseFloat(d.y_label + 10);
//		 		}
		 		return parseFloat(y_pos + 10);
		 	})
		 	.text(function(d){
		 		if (typeof d.sd_value == "undefined") {
		 			return;
		 		}
		 		var sd_value = parseFloat(2*d.sd_value);
		 		var sd_text = "+/- " + sd_value;
		 		return sd_text;
		 	});	
		var line1 = "M " + x0 + "," + y0 + " L " + x1 + "," + y1, 
			bezier = " C " + x2 + "," + y2 + " " + x3 + "," + y3 + " " + x4 + "," + y4,
			line2 = " L " + x5 + "," + y5;	
		return  line1 + bezier + line2;
	}
}

// Returns the x/y position of link_center / left/right/vert_shift
function handles_positions(d){
	var source_orientation = nodes[d.source].orientation,
		target_orientation = nodes[d.target].orientation;		
    var xs = +d3.select('#gg_node' + d.source).attr("x"),
    	ys = +d3.select('#gg_node' + d.source).attr("y"),
    	xt = +d3.select('#gg_node' + d.target).attr("x"),
    	yt = +d3.select('#gg_node' + d.target).attr("y");
    var source_order = nodes[d.source].output_links.indexOf(d.id),
		target_order = nodes[d.target].input_links.indexOf(d.id);
	xs += default_node_size;
	ys += scale(nodes[d.source].output_offsets[source_order] + d.value/2);	
	yt += scale(nodes[d.target].input_offsets[target_order] + d.value/2);	
    if (source_orientation == 'vertical' && target_orientation == 'vertical' && xt <= xs) {
    	// Recycling: 3 handles = left_horiz_shift, right_horiz_shif, vert_shift
    	if (!d.left_horiz_shift){
    		d.left_horiz_shift = 0;
    	}
    	if (!d.right_horiz_shift){
    		d.right_horiz_shift = 0;
    	}
    	if (!d.vert_shift){
    		d.vert_shift = 0;
    	}
    	x_left = xt - default_horiz_shift + d.left_horiz_shift - scale(d.value); // x14 in drawCurve()
    	x_right = xs + default_horiz_shift + d.right_horiz_shift + scale(d.value); // x2 in drawCurve()
    	y_vert = Math.max(ys,yt) + scale(2*d.value) + d.vert_shift; // y8 in drawCurve()
    	var vert = 'translate(' + (x_left + (x_right-x_left)/2 - default_node_size/2) + ', ' + (y_vert - default_node_size/2) + ')',
    		left = 'translate(' + (x_left - default_node_size/2)  + ' ,' + (yt + (y_vert-yt)/2 - default_node_size/2) + ')',
    		right = 'translate(' + (x_right - default_node_size/2) + ' ,' + (ys + (y_vert-ys)/2 - default_node_size/2) + ')';
    	return [vert,left,right];
    }  
    else if (source_orientation == 'vertical' && target_orientation == 'vertical' && xt > xs) {
    	// Classic: 1 handle = link_center
    	if (!d.x_center) {
			d.x_center = 1/2;
		}
		var x_center_draw = (xt-xs) * d.x_center + xs;
			y_center_draw = (ys+yt)/2 - default_node_size/2;
		return "translate(" + x_center_draw + ", " + y_center_draw + ")";
    }	
}

// jQuery AJAX REQUEST TO SAVE LAYOUT
$(function(){ // Wait for the DOM to be ready.
	$("#save_layout_button").click(function(){
		$.post(
			to_main_dir + "sources/php_sankey/save_layout.php",
			{p_title: document.getElementById("diagram_title").value, p_nodes: nodes},
			function(data){
				save_links();
			}
		);
	});	
});

function save_links() {
	$.post(
		to_main_dir + "sources/php_sankey/save_links.php",
		{p_links: links},
		function(data){
			save_filtered();
		}
	);
}

function save_filtered() {
	$.post(
		to_main_dir + "sources/php_sankey/save_filtered.php",
		{p_filtered_nodes: filtered_nodes, p_filtered_links: filtered_links},
		function(data){
			save_env();
		}
	);
}

function save_env() {
	var user_scale = parseInt(document.scale_info.scale.value),
		filter_range = parseInt(document.getElementById("filter_id").max);
	$.post(
		to_main_dir + "sources/php_sankey/save_env.php",
		{p_scale: user_scale, p_filter: current_filter, p_filter_range: filter_range},
		function(data){
			document.getElementById("download_link").click();
		}
	);
}

// UPDATE NODE   
function update_node() {
	if (selected_item.substring(0,4) == "node") {
		var name = document.node_info.name.value,
			color = document.node_info.color.value,
			orientation = $('input[name="orientation"]:checked').val(),
			id = selected_item.substring(4);
		d3.select("#" + selected_item)
			.attr("fill",color);
		if (name.indexOf("<br>") == -1)	{
			d3.select("#gg_" + selected_item + " text")
				.text(name);
		}
		else {
			var name_lines = name.split("<br>"),
				x = +d3.select("#gg_" + selected_item + " text").attr("x"),
				y = +d3.select("#gg_" + selected_item + " text").attr("y"),
				line_break = 11;
			name_lines.forEach(function(line,i){
				if (i == 0) {
					d3.select("#gg_" + selected_item + " text")
				.text(line);
				}
				else {
					d3.select("#gg_" + selected_item + " text")
						.append("tspan")
						.attr("x",x)
						.attr("dy",line_break)
						.text(line);	
				}
			});
		}
		if (nodes[id].name != name) {
			nodes[id].name = name;
			nodes[id].input_links.forEach(function(link_id){
				links[link_id].target_name = name;
			});
			nodes[id].output_links.forEach(function(link_id){
				links[link_id].source_name = name;
			});
		}
		nodes[id].color = color;	
		if (nodes[id].orientation != orientation) {
			nodes[id].orientation = orientation;
			var node_width = d3.select("#" + selected_item).attr("width"),
				node_height = d3.select("#" + selected_item).attr("height");
			d3.select("#" + selected_item)
				.attr("width", node_height)
				.attr("height", node_width)
				.on("click",function(){
					deselect_nodes_and_links();
        			d3.select(this).attr("class","selected_node");
        			selected_item = "node" + id;
        			document.node_info.color.value = d3.select(this).attr("fill");
        			document.node_info.name.value = d3.select("#gg_node" + id + " text").text();
        			$('input[value="' + orientation + '"]').prop("checked",true);
    				return;
				});	
			links.forEach(function(link,link_id){
				if (link.source == id || link.target == id) {
					d3.select("#link" + link_id).attr("d",function(d){
						return drawCurve(d);
					});
				}
			});
		}
	}
}

// UPDATE LINK   
function update_link(link_index) {
	if (link_index == undefined && selected_item.substring(0,4) == "link") {
		
		var source = document.link_info.source.value,
 			target = document.link_info.target.value,
			value = document.link_info.value.value,
			color = document.link_info.color.value,
			id = parseInt(selected_item.substring(4));
		
		if (parseInt(source) != links[id].source){
			// Remove link from old source
			var link_pos = nodes[links[id].source].ouput_links.indexOf(id);
			nodes[links[id].source].ouput_links.splice(link_pos,1);
			// Update source
			links[id].source = parseInt(source);
			links[id].source_name = nodes[links[id].source].name;
			// Add link to new source
			nodes[links[id].source].output_links.push(id);			
		}
		if (parseInt(target) != links[id].target){
			// Remove link from old target
			var link_pos = nodes[links[id].target].input_links.indexOf(id);
			nodes[links[id].target].input_links.splice(link_pos,1);
			// Update target
			links[id].target = parseInt(target);
			links[id].target_name = nodes[links[id].target].name;
			// Add link to new target
			nodes[links[id].target].input_links.push(id);			
		}
		
		links[id].value = parseFloat(value); 
		links[id].color = color;
		recompute_nodes();
		redraw_nodes_and_links();
	}
	else if (link_index >=0) {
		var source = links[link_index].source,
	 		target = links[link_index].target,
			value = links[link_index].value,
			color = links[link_index].color;
		
		d3.select("#link" + link_index)
			.attr("d", function(d){
				return drawCurve(d);
			})
			.attr("stroke-width", scale(value))
     		.attr("stroke",color);
			
		var x_pos = 1/2 * (parseInt(d3.select("#gg_node" + source).attr("x")) + parseInt(d3.select("#gg_node" + target).attr("x"))),
 			y_pos = 1/2 * (parseInt(d3.select("#gg_node" + source).attr("y")) + parseInt(d3.select("#gg_node" + target).attr("y")));
	 	d3.select("#link_value" + link_index)
	 		.attr("x",x_pos)
	 		.attr("y",y_pos)
	 		.text(value);
	}
}

// DELETE NODE   
function delete_node(node_id) {
	var id,
		go = false;
	if (selected_item.substring(0,4) == "node" && node_id == undefined) {
		id = parseInt(selected_item.substring(4));
		go = true;
	}
	else if (node_id >= 0) {
		id = node_id;
		go = true;
	}
	if (go == true) {
		// delete links originating from / going to the deleted node
		var i=0;
		while (i < links.length) {
			console.log("link"+i);
			if (links[i].source == id) {
				console.log(1);
				delete_link(links[i].id);
				i -= 1;
			}
			else if (links[i].target == id) {
				console.log(2);
				delete_link(links[i].id);
				i -= 1;
			}
			i += 1;
		}
	 	
	 	// delete node and shift numerotation
	 	nodes.splice(id,1);
	 	numerotate_nodes();
	 	
	 	// shift source and target of links and update links
		links.forEach(function(link){
			if (link.source > id) {
				link.source -= 1;
			}
			if (link.target > id) {
				link.target -= 1;
			}
		});	
	 	
	 	g_nodes.selectAll(".gg_nodes").remove();
		add_nodes_auto();
		redraw_nodes_and_links();
	}
}

// DELETE LINK    
function delete_link(link_id) {
	var id,
		go = false;
	if (selected_item.substring(0,4) == "link" && link_id == undefined) {
		id = parseInt(selected_item.substring(4));
		go = true;
	}
	else if (link_id >= 0) {
		id = link_id;
		go = true;
	}
	if (go == true) {
		links.splice(id,1);
		numerotate_links();
		recompute_nodes(id);
		g_links.selectAll(".gg_links").remove();
		add_links();
	}
}

// COMPUTE NODES (INPUTS AND OUTPUTS)
function compute_nodes() {
	nodes.forEach(function(node,node_index){
		node.input_links = [];
		node.output_links = [];
		node.total_input_offset = 0;
		node.input_offsets = [0];
		node.total_output_offset = 0;
		node.output_offsets = [0];
		links.forEach(function(link,link_id){
			if (link.target == node_index) {
				node.input_links.push(link_id);
				node.total_input_offset += parseInt(link.value);
				node.input_offsets.push(node.total_input_offset);
			}
			if (link.source == node_index) {
				node.output_links.push(link_id);
				node.total_output_offset += parseInt(link.value);
				node.output_offsets.push(node.total_output_offset);
			}
		});
	});
}

// RECOMPUTE NODES (INPUTS AND OUTPUTS) WHEN A LINK IS DELETED
function recompute_nodes(deleted_link_id) {
	nodes.forEach(function(node) {
		for (var i = node.input_links.length - 1; i >= 0; i--) {
			var link_id = node.input_links[i];
			if (link_id == deleted_link_id) {
				node.input_links.splice(i,1);
			}
		}
		for (var i = node.output_links.length - 1; i >= 0; i--) {
			var link_id = node.output_links[i];
			if (link_id == deleted_link_id) {
				node.output_links.splice(i,1);
			}
		}
		node.input_links.forEach(function(link_id,i){
			if (link_id > deleted_link_id) {
				node.input_links[i] = link_id - 1;
			}
		});
		node.output_links.forEach(function(link_id,i){
			if (link_id > deleted_link_id) {
				node.output_links[i] = link_id - 1;
			}
		});
		recompute_node(node.id,true);
	});
}

// Recompute a single node based on links list (after swap or defilter).
function recompute_node(node_id, no_draw) {
	var node = nodes[node_id];
	node.total_input_offset = 0;
	node.input_offsets = [0];
	node.total_output_offset = 0;
	node.output_offsets = [0];
	node.input_links.forEach(function(link_id){
		var the_value = links.filter(function(link) { 
				return link.id == link_id; 
			});
		node.total_input_offset += parseFloat(the_value[0].value);
		node.input_offsets.push(node.total_input_offset);
	});
	node.output_links.forEach(function(link_id){
		var the_value = links.filter(function(link) {
				return link.id == link_id; 
			});
		node.total_output_offset += parseFloat(the_value[0].value);
		node.output_offsets.push(node.total_output_offset);
	});
	if (no_draw == undefined) {
		redraw_nodes_and_links();
	}
}

// REDRAW NODES AND LINKS (AFTER SCALE UPDATE)
function redraw_nodes_and_links() {
	nodes.forEach(function(node,index) {
		if (node.orientation == "vertical") {
			var height = Math.max(node.total_input_offset,node.total_output_offset);
			if (height == 0) {
				height = default_node_size;
			}
			d3.select("#node" + index)
				.attr("height",scale(height))
				.attr("width",default_node_size);
		}
		else {
			var width = Math.max(node.total_input_offset,node.total_output_offset);
			if (width == 0) {
				width = default_node_size;
			}
			d3.select("#node" + index)
				.attr("width",scale(width))
				.attr("height",default_node_size);
		}
	});

	d3.selectAll(".gg_links").remove();
	add_links();
}

// 'INDEX OF' FUNCTION : javascript nodes[i].input_links.indexOf(link_id) works in console but not in the program...

function my_index_of(my_array,needle) {
	var position = -1;
	my_array.forEach(function(element,index){
		if (element == needle) {
			position = index;
		}
	});
	return position;
}

// ARRAY SWAP METHOD
Array.prototype.swap = function (x,y) {
  var temp = this[x];
  this[x] = this[y];
  this[y] = temp;
  return this;
}


// FILTER
var current_filter = 0,
	previous_filter = 0;
	filtered_nodes = [],
	filtered_links = [],
	filtered_nodes_names = [];

// put current max to 0 to start.
d3.select("input[type=range]").attr("max",0);

d3.select("input[type=range]").on("change", function() {
	previous_filter = current_filter;
	current_filter = parseInt(this.value);
	document.getElementById("current_filter").innerHTML = current_filter;
	filter_links_and_nodes();
});

function filter_links_and_nodes() {
	if (current_filter > previous_filter) {
		// go through nodes an links lists, add the ones lower than the filter in the filtered lists and delete them.
		for (var i = links.length - 1; i >= 0; i--) {
			if (links[i].value < current_filter) {
				filtered_links.push(links[i]);
				delete_link(links[i].id);
			}
		};
		
		for (var i = nodes.length - 1; i >= 0; i--) {
			if (Math.max(nodes[i].total_input_offset, nodes[i].total_output_offset) < current_filter) { 
				filtered_nodes.push(nodes[i]);
				update_filtered_nodes_names();
				delete_node(nodes[i].id);
			}
		};
		redraw_nodes_and_links();
	}
	else if (current_filter < previous_filter) {
		for (var i = filtered_links.length - 1; i >= 0; i--) {
			var link = filtered_links[i];
			if (link.value >= current_filter) {
				link.source = nodes_names.indexOf(link.source_name);
				if (link.source == -1) {
					// add filtered source node
					var filtered_id = filtered_nodes_names.indexOf(link.source_name);
					nodes.push(filtered_nodes[filtered_id]);
					filtered_nodes.splice(filtered_id,1);
					update_filtered_nodes_names();
					numerotate_nodes();
					link.source = nodes.length - 1;
					add_nodes_auto();
				}
				link.target = nodes_names.indexOf(link.target_name);
				if (link.target == -1) {
					// add filtered target node
					
					var filtered_id = filtered_nodes_names.indexOf(link.target_name);
					nodes.push(filtered_nodes[filtered_id]);
					filtered_nodes.splice(filtered_id,1);
					update_filtered_nodes_names();
					numerotate_nodes();
					link.target = nodes.length - 1;
					add_nodes_auto();
				}
				// add link
				link.id = links.length;
				nodes[link.source].output_links.push(link.id);
				nodes[link.target].input_links.push(link.id);
				links.push(link);
				recompute_node(link.source,true);
				recompute_node(link.target,true);
				filtered_links.splice(i, 1);
				add_links();
			}
		};
	}
}

// update filtered nodes names (called by filter_links_and_nodes)
function update_filtered_nodes_names() {
	filtered_nodes_names = [];
	filtered_nodes.forEach(function(node){
		filtered_nodes_names.push(node.name);
	});
}

// ALT KEY INTERACTION: MOVE LABELS
var alt_key_pressed = false;
window.focus();
d3.select(window).on("keydown",function() {
	if (d3.event.keyCode == 18) {
		alt_key_pressed = true;
		window.focus();
	};
});
d3.select(window).on("keyup",function() {
	if (d3.event.keyCode == 18) {
		alt_key_pressed = false;
		window.focus();
	}
});

// function drag_text
function drag_text(dragged) {
	var old_x = +d3.select(dragged).attr("x"),
		old_y = +d3.select(dragged).attr("y"),
		new_x = old_x + d3.event.dx,
		new_y = old_y + d3.event.dy;
	d3.select(dragged).attr("x", new_x);
    d3.select(dragged).attr("y", new_y);
    // Change link or node attributes
    var id = d3.select(dragged).attr("id");
    if (id.substring(0,4) ==  "text") {
    	id = id.substring(4);
    	nodes[id].x_label = new_x;
    	nodes[id].y_label = new_y;
    	d3.select(dragged).selectAll("tspan").attr("x",new_x);
    }
    else if (id.substring(0,10) ==  "link_value") {
    	id = id.substring(10);
    	links[id].x_label = new_x;
    	links[id].y_label = new_y;
    }
    else if (id.substring(0,13) ==  "link_sd_value") {
    	id = id.substring(13);
    	links[id].x_sd_label = new_x;
    	links[id].y_sd_label = new_y;
    }
}

// AGGREGATE OR DESAGGREGATE THE FLOWS
d3.select("#aggregation_info").on("change", function() {
	if (document.getElementById("aggregation_info").checked) {
		aggregate();
	}
	else {
		desaggregate();
	}
});

var desaggregated_nodes,
	desaggregated_links,
	desaggregated_nodes_names,
	desag_filter_position;

function aggregate() {
	// Save desaggregated nodes and links
	desaggregated_nodes = deep_clone_array(nodes);
	desaggregated_links = deep_clone_array(links);
	desaggregated_nodes_names = deep_clone_array(nodes_names);
	// Save filter's position
	desag_filter_position = parseInt(document.getElementById("current_filter").value);
	// Remove desaggregated Sankey diagram
	d3.selectAll(".gg_nodes").remove();
	d3.selectAll(".gg_links").remove();
	// Compute aggregated nodes and links
	nodes = build_aggregated_nodes();
	links = build_aggregated_links();
	numerotate_links();
	compute_nodes();
	// Draw aggregated Sankey diagram
	add_nodes_auto();
	add_links();
}

function desaggregate() {
	// Remove aggregated Sankey diagram
	d3.selectAll(".gg_nodes").remove();
	d3.selectAll(".gg_links").remove();
	// Use desagreggated information again
	nodes = deep_clone_array(desaggregated_nodes);
	links = deep_clone_array(desaggregated_links);
	nodes_names = deep_clone_array(desaggregated_nodes_names);
	// Draw desaggregated Sankey diagram
	add_nodes_auto();
	add_links();
	// Reset previous filter
	d3.select("input[type=range]").attr("value", desag_filter_position);
	document.getElementById("current_filter").value = desag_filter_position;
	
}

function build_aggregated_nodes() {
	var n = [];
	var names = [];
	nodes.forEach(function(node){
		if (names.indexOf(node.merged_name) == -1 && node.merged_name != "idem") {
			names.push(node.merged_name);
			node.name = node.merged_name;
			node.id = n.length;
			n.push(node);
		}
		else if (node.merged_name == "idem") {
			names.push(node.name);
			node.id = n.length;
			n.push(node);
		}
	});
	nodes_names = names;
	return n;
}

function build_aggregated_links() {
	var l1 = [],
		distinct_sources_targets = [],
		l2 = [];
	links.forEach(function(link){
		var old_source = link.source,
			old_target = link.target;
		link.source = nodes_names.indexOf(link.source_name);
		if (link.source == -1) {
			// Try with corresponding merged_name or print error.
			var merged_name = desaggregated_nodes[old_source].merged_name;
			link.source = nodes_names.indexOf(merged_name);
			if (link.source == -1) {
				console.log("link" + link.id + ": no match for source name " + link.source_name);
			}
		}
		link.target = nodes_names.indexOf(link.target_name);
		if (link.target == -1) {
			// Try with corresponding merged_name or print error.
			var merged_name = desaggregated_nodes[old_target].merged_name;
			link.target = nodes_names.indexOf(merged_name);
			if (link.target == -1) {
				console.log("link" + link.id + ": no match for target name " + link.target_name);
			}
		}
		var temp = distinct_sources_targets.filter(function(couple){
			return couple[0] == link.source && couple[1] == link.target;
		});
		if (temp[0] == undefined) {
			distinct_sources_targets.push([link.source,link.target]);
		}
	});
	l1 = links;
	distinct_sources_targets.forEach(function(couple){
		var first = true;
		var the_link = {};
		l1.forEach(function(link) {
			if (link.source == couple[0] && link.target == couple[1] && first == true) {
				the_link = link;
				first = false;
			}
			else if (link.source == couple[0] && link.target == couple[1]) {
				the_link.value += parseFloat(link.value);
			}
		});
		l2.push(the_link);
	});
	return l2;
}

// ____________________________________________________________________________________
// ____________________________________________________________________________________

function numerotate_nodes() {
	nodes_names = [];
	nodes.forEach(function(node,i){
		node.id = i;
		nodes_names.push(node.name);
	});
}

function set_nodes_names() {
	nodes_names = [];
	nodes.forEach(function(node,i){
		nodes_names.push(node.name);
	});
}

function numerotate_links() {
	links.forEach(function(link,i){
		link.id = i;
	});
}

function set_links_source_target_ids() {
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
}

// AUTO function add_new_node_auto 
function add_nodes_auto() {
		
   	var gg_nodes = g_nodes.selectAll(".gg_nodes").data(nodes).enter().append("g")
   		.attr("id",function(d,i){
   			return "gg_node" + i;
   		})
   		.attr("class","gg_nodes");
   	  
   	gg_nodes.attr("x",function(d){
   			// if the nodes have been moved then filtered they will keep their position when put back.
   			if (d.x_filter_pos == undefined) {
   				return d.x;
   			}
   			else {
   				return d.x_filter_pos;
   			}	
   		})
   		.attr("y",function(d){
   			if (d.y_filter_pos == undefined) {
   				return d.y;
   			}
   			else {
   				return d.y_filter_pos;
   			}
   		})
   		.attr("transform",function(d) {
   			if (d.y_filter_pos == undefined || d.x_filter_pos == undefined) {
   				return "translate(" + d.x + ", " + d.y + ")";
   			}
   			else {
   				return "translate(" + d.x_filter_pos + ", " + d.y_filter_pos + ")";
   			}
   			
   		})			
   		.call(d3.behavior.drag()
   			.origin(Object).on("drag", function() {
				drag_node(this);
			})
		);
   	
   	gg_nodes.append("rect")
    	.attr("class", "node")
        .attr("id", function(d,i){
        	return "node" + i;
        })
		.attr("width", default_node_size)
		.attr("height", default_node_size)
        .attr("fill",function(d){
        	return d.color;
        })
        .on("mouseover", function(){
        	d3.select(this).attr("class","selected_node");
        })
        .on("mouseout", function(){
        	d3.select(this).attr("class","node");
        })
        .on("click", function(d,i){
        	deselect_nodes_and_links();
        	d3.select(this).attr("class","selected_node");
        	selected_item = "node" + i;
        	document.node_info.color.value = d.color;
        	document.node_info.name.value = d.name;
        	$('input[value="' + d.orientation + '"]').prop("checked",true);
    		return;
        })
        .each(function(d,i){
        	d3.select(this).append("title")
        		.text(i);
        });
        
   gg_nodes.append("text")
   		.attr("id",function(d,i){
   			return "text" + i;
   		})
    	.attr("x",function(d){
    		if (d.x_label) {
    			return d.x_label;
    		}
    		else {
    			return 5;
    		}
    	})
    	.attr("y",function(d){
    		if (d.y_label) {
    			return d.y_label;
    		}
    		else {
    			return 15;
    		}
    	})
    	.attr("style","font-family:Arial; font-size:11px;")
    	.each(function(d){
    		if (d.name.indexOf("<br>") == -1)	{
				d3.select("#gg_node" + d.id + " text").text(d.name);
			}
			else {
				var name_lines = d.name.split("<br>"),
					x = +d3.select("#gg_node" + d.id + " text").attr("x"),
					y = +d3.select("#gg_node" + d.id + " text").attr("y"),
					line_break = 11;
				name_lines.forEach(function(line,i){
					if (i == 0) {
						d3.select("#gg_node" + d.id + " text").text(line);
					}
					else {
						d3.select("#gg_node" + d.id + " text")
							.append("tspan")
							.attr("x",x)
							.attr("dy",line_break)
							.text(line);	
					}
				});
			}
			return;
    	})
    	.on("click", function(d,i){
        	deselect_nodes_and_links();
        	var node_to_select = "#gg_node" + i + " rect";
        	d3.select(node_to_select).attr("class","selected_node");
        	selected_item = "node" + i;
        	document.node_info.color.value = d.color;
        	document.node_info.name.value = d.name;
        	$('input[value="' + d.orientation + '"]').prop("checked",true);
    		return;
        })
    	.call(d3.behavior.drag()
   			.origin(Object).on("drag", function() {
   				if (alt_key_pressed == true) {
					drag_text(this);
   				}
   				else {
   					var node_to_drag = "gg_node" + d3.select(this).attr("id").substring(4,6);
					drag_node(document.getElementById(node_to_drag));
				}
			})
		);      	
}

// FUNCTION MOVE NODES FORWARD
function move_nodes_forward(){
	document.getElementById("svg").appendChild(document.getElementById("g_nodes"));
};

// New function to reorder nodes (layout issues)
function order_nodes_links(){
	old_nodes_ids = [];
	old_links_ids = [];
	new_nodes_ids = [];
	new_links_ids = [];
	nodes.forEach(function(n,i){
		old_nodes_ids.push(n.id);
		new_nodes_ids.push(i);
		n.id = i;
	});
	links.forEach(function(l,i){
		l.source = new_nodes_ids[old_nodes_ids.indexOf(l.source)];
		l.target = new_nodes_ids[old_nodes_ids.indexOf(l.target)];
		old_links_ids.push(l.id);
		new_links_ids.push(i);
		l.id = i;
	});
}