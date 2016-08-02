<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>D3 Test</title>
	<script src="d3/d3.js"></script>
	<script src="d3/topojson.min.js"></script>
	<style>
		* {
			margin: 0;
			padding: 0;
		}
		svg {
			position: absolute;
			width: 100%;
			height: 100%;
		}
		#zones.active {
			fill: #aaf;
		}
		#zone-borders {
		  fill: none;
		  stroke-width: 1px;
		  stroke-linejoin: round;
		  stroke-linecap: round;
		  pointer-events: none;
	  }
		.category-text {
			font-family: Roboto;
			font-size: 36px;
		}
		.min-text, .max-text {
			font-family: Roboto;
			font-size: 15px;
		}
	</style>
</head>
<body>
	<div id="category-div" style="display: none;">
    <?php
      $cats = ["elev", "Temp", "TempC", "Dewp", "Relh",
					"Winds", "SLP", "Altimeter", "Visibility", "TempHi24",
					"TempLo24", "RelhHi24", "RelhLo24", "GustHi24",
					"RawsTemp12", "RawsTemp13", "RawsTemp14", "RawsRelh12",
					"RawsRelh13", "RawsRelh14"];
      $catsLength = count($cats);

      for ($i = 0; $i < $catsLength; $i++)
      {
        if( $_GET[ $cats[$i] ] == "on" )
        {
					echo "<p>";
          echo htmlspecialchars($cats[$i]);
					echo "</p>";
				}
      }
    ?>
	</div>

	<div id="circleOpacity-div" style="display: none;">
		<?php
			$circleOpacity = $_GET[ "circleOpacity" ];
			echo "<p>";
			echo htmlspecialchars($circleOpacity);
			echo "</p>";
		?>
	</div>

	<div id="dt-div" style="display: none;">
		<?php
			$dt = $_GET[ "dt" ];
			echo "<p>";
			echo htmlspecialchars($dt);
			echo "</p>";
		?>
	</div>

	<div id="bgColor-div" style="display: none;">
		<?php
			$bgColor = $_GET[ "bgColor" ];
			echo "<p>";
			echo htmlspecialchars($bgColor);
			echo "</p>";
		?>
	</div>

	<div id="zoneColor-div" style="display: none;">
		<?php
			$zoneColor = $_GET[ "zoneColor" ];
			echo "<p>";
			echo htmlspecialchars($zoneColor);
			echo "</p>";
		?>
	</div>

	<div id="zoneColorActive-div" style="display: none;">
		<?php
			$zoneColorActive = $_GET[ "zoneColorActive" ];
			echo "<p>";
			echo htmlspecialchars($zoneColorActive);
			echo "</p>";
		?>
	</div>

	<div id="borderColor-div" style="display: none;">
		<?php
			$borderColor = $_GET[ "borderColor" ];
			echo "<p>";
			echo htmlspecialchars($borderColor);
			echo "</p>";
		?>
	</div>

	<div id="minTextColor-div" style="display:none">
		<?php
			$minTextColor = $_GET[ "minTextColor" ];
			echo "<p>";
			echo htmlspecialchars($minTextColor);
			echo "</p>";
		?>
	</div>

	<div id="maxTextColor-div" style="display:none">
		<?php
			$maxTextColor = $_GET[ "maxTextColor" ];
			echo "<p>";
			echo htmlspecialchars($maxTextColor);
			echo "</p>";
		?>
	</div>

	<div id="catTextColor-div" style="display:none">
		<?php
			$catTextColor = $_GET[ "catTextColor" ];
			echo "<p>";
			echo htmlspecialchars($catTextColor);
			echo "</p>";
		?>
	</div>

	<div id="radiusMin-div" style="display:none">
		<?php
			$radiusMin = $_GET[ "radiusMin" ];
			echo "<p>";
			echo htmlspecialchars($radiusMin);
			echo "</p>";
		?>
	</div>

	<div id="radiusMax-div" style="display:none">
		<?php
			$radiusMax = $_GET[ "radiusMax" ];
			echo "<p>";
			echo htmlspecialchars($radiusMax);
			echo "</p>";
		?>
	</div>

	<script>
		//begin by getting the variables put into DOM by form/php

		//set categories variable from the form
		var div = document.getElementById("category-div");
		var myData = div.children;

		var categories = ["BUFFER"];
		for (var i = 0; i < myData.length; i++) {
			categories.push(myData[i].textContent);
		}
		categories.push("BUFFER");

		//set opacity
		div = document.getElementById("circleOpacity-div");
		var circleOpacity = parseFloat(div.children[0].textContent);

		//set dt
		div = document.getElementById("dt-div");
		var dt = parseInt(div.children[0].textContent);

		//set bgColor
		div = document.getElementById("bgColor-div");
		var bgColor  = div.children[0].textContent;

		//set zoneColor
		div = document.getElementById("zoneColor-div");
		var zoneColor  = div.children[0].textContent;

		//set zoneColorActive
		div = document.getElementById("zoneColorActive-div");
		var zoneColorActive  = div.children[0].textContent;

		//set borderColor
		div = document.getElementById("borderColor-div");
		var borderColor = div.children[0].textContent;

		//set minTextColor
		div = document.getElementById("minTextColor-div");
		var minTextColor = div.children[0].textContent;

		//set maxTextColor
		div = document.getElementById("maxTextColor-div");
		var maxTextColor = div.children[0].textContent;

		//set catTextColor
		div = document.getElementById("catTextColor-div");
		var catTextColor = div.children[0].textContent;

		//set radiusMin
		div = document.getElementById("radiusMin-div");
		var radiusMin = parseFloat(div.children[0].textContent);

		//set radiusMax
		div = document.getElementById("radiusMax-div");
		var radiusMax = parseFloat(div.children[0].textContent);



		//set up variables and functions we will need
		var width = window.innerWidth;
		var height = window.innerHeight;
		var scale = 9000;

		var projectionFunc = d3.geo.mercator()
			.scale(scale)
			.center([-119.6, 36.3])
			.translate([width/2, height/2]);

		var pathFunc = d3.geo.path()
			.projection(projectionFunc);

		var svg = d3.select("body").append("svg")
			.attr("width", width)
			.attr("height", height);

		svg.append("rect")
			.classed("background", true)
			.attr("x", 0)
			.attr("y", 0)
			.attr("width", width)
			.attr("height", height)
			.attr("fill", bgColor);

		//setup map on bot
		var gMap = svg.append("g")
				.attr("class", "map");

		//setup vis on top
		var gVis = svg.append("g")
				.attr("class", "vis");

		//global vars we'll need
		var zoneNumbers = [];
		var orderedZoneNumbers = [];
		var index = 0;
		// var debug;

		d3.json("hnxSaZones.json", function(error, hnxJson) {
			if (error) { console.log("there was an error loading the data" + error); }

			//take the data and set up basic map stuff
			var zones = topojson.feature(hnxJson, hnxJson.objects.hnxsaZones).features;
			var borders = topojson.mesh(hnxJson, hnxJson.objects.hnxsaZones, function(a, b) { return a !== b; } );

			//put the zone numbers in order
			zones.sort(function(a, b) { return a.properties.ZONE - b.properties.ZONE; })

			// debug = zones;

			gMap.selectAll("path")
					.data(zones)
				.enter()
					.append("path")
					.attr("id", "zones")
					.attr("fill", zoneColor)
					.attr("d", pathFunc);

			//make borders separately for better transitions etc
			gMap.append("path")
					.datum(borders)
					.attr("id", "zone-borders")
					.attr("stroke", borderColor)
					.attr("d", pathFunc);

			//now that we have basic map set up, send data to main func
			main(zones);
		});


		//globals:
		var timeAtZone = dt * categories.length;
		var timePerData = timeAtZone/(categories.length);
		var dataChangeTransition = timePerData/2;
		var i = 0;
		var index = 0;

		function main(mapData) {
			//put all the vis on the svg, make the heatmap, cycle through
			//changing opacity on the categories selected (default all)
			//and cycle through each zone

			//get the data and run initialize() for each category
			var dataUrl = "ba-simple-proxy/ba-simple-proxy.php?url=http://www.wrh.noaa.gov/hnx/JimBGmwXJList.php?extents=34.74,-121.4,38.36,-117.62&mode=native";
			// var dataUrl = "offlineData.json";
			d3.json(dataUrl, function(error, data) {
				if (error) { console.log("There was an error loading the data." + error); }

				//debug = data.stations;

				for (var i = 0; i < categories.length; i++) {
					initialize(data.stations, categories[i])
				}

				//now run through functions
				makeLegend();
				cycleMap(mapData);

				setInterval(function() {
					cycleMap(mapData);
					}, timeAtZone);

				setInterval(function() {
					cycleData();
					}, timePerData);

			});
		}


		function makeLegend() {
			//make a heatmap bar to show what the data represents
			var numRects = 100;
			var rectsData = [];
			for (var i = 0; i < numRects; i++) {
				rectsData.push(i);
			}
			var textHeight = height/15;
			var categoryTextWidth = width/4;
			var colorScale = d3.scale.linear()
					.domain([0, numRects - 1])
					.range([0, 255]);

			gVis.selectAll("rect.heatmap")
					.data(rectsData)
				.enter()
					.append("rect")
					.attr("x", function(d, i) {
						return 2.2*categoryTextWidth + i*(.75*categoryTextWidth/numRects);
					})
					.attr("y", textHeight/2)
					.attr("width", categoryTextWidth/numRects)
					.attr("height", textHeight/2)
					.attr("fill", function(d) {
						var num = Math.round(colorScale(d));
						return "rgb(" + num + ", 0, " + (255 - num) + ")";
					});
		}


		var centered;	//initialize variable for later to see what zone is centered
		function cycleMap(data) {
			//this function picks the next zone and centers the map on it

			var zoneData = data[ index % data.length ]

			//get center of next zone
			var centroid = pathFunc.centroid(zoneData);
			var x = -centroid[0];
			var y = -centroid[1];
			var scaleFactor = 3;	//how far to zoom
			centered = zoneData;	//for changing fill

			//change fill and transition to new transform
			gMap.selectAll("path")
					.attr("fill", function(d) {
						if (centered===d) {
							return zoneColorActive;
						} else {
							return zoneColor;
						}
					});
			gMap
				.transition()
					.duration(timePerData)
					.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")scale(" + scaleFactor + ")translate(" + x + "," + y + ")")

			//make borders lighter
			gMap.selectAll("#zone-borders")
				.style("stroke-width", 1.5/scaleFactor + "px");

			//!!!!!
			index++;
		}


		function cycleData() {
			//pick next data category, make it opacity 1 and the
			//one before it opacity 0 in a smooth transition

			var nextCategory = categories[i % categories.length];
			var previousCategory = categories[(i-1) % categories.length];

			svg.selectAll("#" + previousCategory)		//above
				.transition()
					.duration(dataChangeTransition)
					.style("opacity", 0);


			gMap.selectAll("#" + nextCategory)		//below circs
				.transition()
					.duration(dataChangeTransition)
					.style("opacity", circleOpacity);
			gVis.selectAll("#" + nextCategory)		//below text
				.transition()
					.duration(dataChangeTransition)
					.style("opacity", 1);

			//!!!!!
			i++;
		}


		function average(data) {
			//returns mean of data
			var sum = 0;
			for (var i = 0; i < data.length; i++) {
				sum += data[i];
			}
			var average = sum/data.length;
			return average;
		}


		function initialize(dataFull, name) {
			//from the data, plot the heatmapped dots in their
			//coordinates and start them off opacity 0.
			//also display some text for what the colors mean
			//and what the data is about

			if (name == "BUFFER") {
				return;
			}

			var dataList = [];
			var newNum;
			for (var i = 0; i < dataFull.length; i++) {
				newNum = eval("dataFull[i]." + name);
				if (typeof newNum == "string" && newNum != "") {
					dataList.push( parseFloat(newNum) );
				}
			}

			var avg = average(dataList);

			var diffs = [];
			for (var i = 0; i < dataList.length; i++) {
				diff = Math.abs(dataList[i] - avg)
				diffs.push( Math.pow(diff, 2) );
			}
			var sum = 0;
			for (var i = 0; i < diffs.length; i++) {
				sum += diffs[i];
			}
			var stdv = Math.sqrt(sum/diffs.length);

			dataList.sort( function(a, b) { return a-b; });

			for (var i = 0; i < dataList.length; i++) {
				if (dataList[i] > (avg - 3*stdv)) {
					var min = dataList[i];
					break;
				}
			}
			for (var i = 0; i < dataList.length; i++) {
				if (dataList[dataList.length - i - 1] < (avg + 3*stdv)) {
					var max = dataList[dataList.length - i - 1];
					break;
				}
			}

			var colorScale = d3.scale.linear()
					.domain([min, max])
					.range([0, 255]);

			var radiusScale =	d3.scale.linear()
							.domain([min, max])
							.range([radiusMin, radiusMax]);

			//add data map (for zoom) with class 'name' and opacity 0
			gMap.selectAll("circle." + name)
					.data(dataFull)
				.enter()
					.append("circle")
					.attr("class", function(d) {
						if (eval("d." + name)) {
							return name;
						} else {
							return "undefined";
						}
					})
					.attr("id", name)
					.attr("cx", function(d) {
						return projectionFunc([d.longitude, d.latitude])[0]
					})
					.attr("cy", function(d) {
						return projectionFunc([d.longitude, d.latitude])[1]
					})
					.attr("r", function(d) {
						var r = radiusScale( eval("d." + name) );
						if (r <= radiusMax && r >= radiusMin) {
							return r;
						} else {
							return 0;
						}
					})
					.attr("fill", function(d) {
						var num = Math.round(colorScale(eval("d." + name)));
						if (num < 0 || num > 255) {
							return "rgb(0, 0, 0)";
						}
						return "rgb(" + num + ", 0, " + (255 - num)+ ")";
					})
					.style("opacity", 0);

			//the ones that had no data
			gMap.selectAll("circle.undefined")
					.remove();

			//display name of category
			var textHeight = height/15;
			var categoryTextWidth = width/4;
			gVis.selectAll("text#" + name + ".category-text")
					.data([0])
				.enter()
					.append("text")
					.attr("class", "category-text")
					.attr("id", name)
					.attr("x", categoryTextWidth)
					.attr("y", textHeight)
					.attr("fill", catTextColor)
					.text(name)
					.style("opacity", 0);

			//show a legend for the color map
			gVis.selectAll("text#" + name + ".min-text")
					.data([0])
				.enter()
					.append("text")
					.attr("class", "min-text")
					.attr("id", name)
					.attr("x", 2*categoryTextWidth)
					.attr("y", textHeight)
					.attr("fill", minTextColor)
					.text(min)
					.style("opacity", 0);

			gVis.selectAll("text#" + name + ".max-text")
					.data([0])
				.enter()
					.append("text")
					.attr("class", "max-text")
					.attr("id", name)
					.attr("x", 3*categoryTextWidth)
					.attr("y", textHeight)
					.attr("fill", maxTextColor)
					.text(max)
					.style("opacity", 0);
		}
	</script>
</body>
</html>
