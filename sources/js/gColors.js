var gColors_array = ["#3366cc", "#dc3912", "#ff9900", "#0D9912", "#8F0598", "#009FBD", "#E34278", "#65AB00", "#B92C25", "#2F6496", "#9C4299", "#23A9A0", "#AFA812", "#6A2FD5", "#E37305", "#900502", "#6C0C70", "#279860", "#5175A7", "#3641AD", "#AF752C", "#19D423", "#B71383", "#FF2CA4", "#9E5834", "#A9C415", "#376E95", "#629014", "#B8A51A", "#075D1C", "#7A310E", "#3266D3", "#E9330C"];

function gColors(i) {
	var j = i % gColors_array.length;
	return gColors_array[j];
}