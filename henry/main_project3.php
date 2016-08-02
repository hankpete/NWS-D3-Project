<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>D3 Test</title>
	<script src="d3/d3.min.js"></script>
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

		#zone-borders {
		  fill: none;
		  stroke-width: 1px;
		  stroke-linejoin: round;
		  stroke-linecap: round;
		  pointer-events: none;
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

<div id="dt-div" style="display: none;">
	<?php
		$dt = $_GET[ "dt" ];
		echo "<p>";
		echo htmlspecialchars($dt);
		echo "</p>";
	?>
</div>

<div id="delay-div" style="display: none;">
	<?php
		$delay = $_GET[ "delay" ];
		echo "<p>";
		echo htmlspecialchars($delay);
		echo "</p>";
	?>
</div>

<div id="showIds-div" style="display: none;">
	<?php
		$showIds = $_GET[ "showIds" ];
		echo "<p>";
		echo htmlspecialchars($showIds);
		echo "</p>";
	?>
</div>

<div id="showCities-div" style="display: none;">
	<?php
		$showCities = $_GET[ "showCities" ];
		echo "<p>";
		echo htmlspecialchars($showCities);
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

<div id="borderColor-div" style="display: none;">
	<?php
		$borderColor = $_GET[ "borderColor" ];
		echo "<p>";
		echo htmlspecialchars($borderColor);
		echo "</p>";
	?>
</div>

<div id="zoneTextColor-div" style="display:none">
	<?php
	 	$zoneTextColor = $_GET[ "zoneTextColor" ];
		echo "<p>";
		echo htmlspecialchars($zoneTextColor);
		echo "</p>";
	?>
</div>

<div id="cityTextColor-div" style="display:none">
	<?php
		$cityTextColor = $_GET[ "cityTextColor" ];
		echo "<p>";
		echo htmlspecialchars($cityTextColor);
		echo "</p>";
	?>
</div>

<div id="inactiveRad-div" style="display:none">
	<?php
		$inactiveRad = $_GET[ "inactiveRad" ];
		echo "<p>";
		echo htmlspecialchars($inactiveRad);
		echo "</p>";
	?>
</div>

<div id="activeRad-div" style="display:none">
	<?php
		$activeRad = $_GET[ "activeRad" ];
		echo "<p>";
		echo htmlspecialchars($activeRad);
		echo "</p>";
	?>
</div>

<div id="inactiveCirc-div" style="display:none">
	<?php
		$inactiveCirc = $_GET[ "inactiveCirc" ];
		echo "<p>";
		echo htmlspecialchars($inactiveCirc);
		echo "</p>";
	?>
</div>

<div id="activeCirc-div" style="display:none">
	<?php
		$activeCirc = $_GET[ "activeCirc" ];
		echo "<p>";
		echo htmlspecialchars($activeCirc);
		echo "</p>";
	?>
</div>

<script>
	//begin by getting the variables put into DOM by form/php

	//set categories variable from the form
	var div = document.getElementById("category-div");
	var myData = div.children;

	var cats = [];
	for (var i = 0; i < myData.length; i++) {
		cats.push(myData[i].textContent);
	}

	//set transitionTime
	div = document.getElementById("dt-div");
	var transitionTime = parseInt(div.children[0].textContent);

	//set delay
	div = document.getElementById("delay-div");
	var delay = parseInt(div.children[0].textContent);

	//set showIds
	div = document.getElementById("showIds-div");
	var showIds = div.children[0].textContent;

	//set showCities
	div = document.getElementById("showCities-div");
	var showCities = div.children[0].textContent;

	//set bgColor
	div = document.getElementById("bgColor-div");
	var bgColor  = div.children[0].textContent;

	//set zoneColor
	div = document.getElementById("zoneColor-div");
	var zoneColor  = div.children[0].textContent;

	//set borderColor
	div = document.getElementById("borderColor-div");
	var borderColor = div.children[0].textContent;

	//set zoneTextColor
	div = document.getElementById("zoneTextColor-div");
	var zoneTextColor = div.children[0].textContent;

	//set cityTextColor
	div = document.getElementById("cityTextColor-div");
	var cityTextColor = div.children[0].textContent;

	//set inactiveRad
	div = document.getElementById("inactiveRad-div");
	var inactiveRad = parseFloat(div.children[0].textContent);

	//set activeRad
	div = document.getElementById("activeRad-div");
	var activeRad = parseFloat(div.children[0].textContent);

	//set inactiveCirc
	div = document.getElementById("inactiveCirc-div");
	var inactiveCirc = div.children[0].textContent;

	//set activeCirc
	div = document.getElementById("activeCirc-div");
	var activeCirc = div.children[0].textContent;



	//set up variables and functions we will need
	var width = window.innerWidth;
	var height = window.innerHeight;
	var xScale = d3.scale.linear()
			.domain([0, 100])
			.range([0, width]);
	var yScale = d3.scale.linear()
			.domain([0, 100])
			.range([0, height]);

	var scale = 4000;
	var projectionFunc = d3.geo.mercator()	//world
		.scale(scale)
		.center([-119.6, 36.3])	//hnx coords
		.translate([xScale(15), yScale(70)]);

	var pathFunc = d3.geo.path()
		.projection(projectionFunc);

	var svg = d3.select("body").append("svg")
		.attr("width", width)
		.attr("height", height);

	svg.append("rect")
		.attr("x", "0")
		.attr("y", "0")
		.attr("width", width)
		.attr("height", height)
		.attr("fill", bgColor);

	var gMap = svg.append("g");
	var gCams = svg.append("g");

	//get hnx map data and plot it
	d3.json("hnxSaZones.json", function(error, hnx) {
		if (error) throw error;

		var zones = topojson.feature(hnx, hnx.objects.hnxsaZones).features;
		gMap.selectAll("path")
				.data(zones)
			.enter()
				.append("path")
				.attr("id", "zones")
				.attr("fill", zoneColor)
				.attr("d", pathFunc);

		gMap.append("path")
	    	.datum(topojson.mesh(hnx, hnx.objects.hnxsaZones, function(a, b) {
			    return a !== b;
		    }))
	      .attr("id", "zone-borders")
				.attr("stroke", borderColor)
	      .attr("d", pathFunc);

	});


	// var debug;
	d3.json("offlineData.json", function(error, data) {
		//set up the data and put the dots down
		if (error) { console.log("An error has occured:" + error); }

		//set up the data
		var camLinks = ["1.jpg", "2.jpg", "3.jpg", "4.jpg", "5.jpg"];
		var camJson = [];
		var stations = data.stations;
		var stationsList = [];
		for (var i = 0; i < camLinks.length; i++) {
			camJson.push( { "camLink" : camLinks[i], "LAT" : stations[i].latitude, "LON" : stations[i].longitude } );
		}
		for (var i = 0; i < camLinks.length; i++) {
			stationsList.push(stations[i]);
		}
		// debug = camJson;

		//
		//initialize all of the vis stuff
		//
    gMap.selectAll("circle.camPoints")
	    	.data(camJson)
	    .enter()
	    	.append("circle")
	    	.classed("camPoints", true)
				.attr("id", function(d, i) { return "pt" + i; })
	    	.attr("cx", function(d) {
	    		return projectionFunc( [d.LON, d.LAT] )[0];
	    	})
	    	.attr("cy", function(d) {
	    		return projectionFunc( [d.LON, d.LAT] )[1];
	    	})
	    	.attr("r", inactiveRad)
	    	.attr("fill", inactiveCirc);

		//put cams on svg transparent
		gCams.selectAll("image.cam")
				.data(camJson)
			.enter()
				.append("image")
				.classed("cam", true)
				.attr("id", function(d, i) { return "vis" + i; })
				.attr("x", xScale(40))
				.attr("y", yScale(10))
				.attr("xlink:href", function(d) { return d.camLink; })
				.attr("width", xScale(50))
				.attr("height", yScale(50))
				.style("opacity", 0);

		//some descriptive text
		var textX = xScale(10);
		var textY = yScale(10);
		var cats = ["elev", "Temp", "TempC", "Dewp", "Relh",
				"Winds", "Visibility", "TempHi24",
				"TempLo24", "RelhHi24", "RelhLo24"];
		gCams.selectAll("rect.cats")
					.data(stationsList)
			.enter()
				.append("rect")
				.classed("cats", true)
				.attr("id", function(d, i) { return "stats" + i; })
				.attr("x", textX - 30)
				.attr("y", textY - 35)
				.attr("width", 275)
				.attr("height", 25*cats.length)
				.style("opacity", 0);
		gCams.selectAll("text.title")
			.data(stationsList)
		  .enter()
		  	.append("text")
			.classed("cam", true)
			.attr("id", function(d, i) { return "stats" +i; })
			.attr("x", textX)
			.attr("y", textY)
			.attr("fill", "white")
			.attr("font-family", "Roboto")
			.attr("font-size", 20)
			.text(function(d, i) { return "Data for cam #" + (i + 1) + ":"; })
			.style("opacity", 0);
		var newY = textY;
		var newX = textX + 10;
		for (var j = 0; j < cats.length; j++) {
				newY += 20;
		    gCams.selectAll("text." + cats[j])
		    	.data(stationsList)
		      .enter()
		      	.append("text")
		    	.classed(cats[j], true)
		    	.attr("id", function(d, i) { return "stats" + i; })
		    	.attr("x", newX)
				.attr("y", newY)
		    	.attr("fill", "white")
				.attr("font-family", "Roboto")
				.attr("font-size", 15)
		    	.text(function(d) { return cats[j] + ": " + eval("d." + cats[j]); })
		    	.style("opacity", 0);
		}

		//run
		main(camJson);
	});

	//globals
	var index = 0;


	function main(data) {
		cycle(data);
		setInterval(function() {
			cycle(data);
		}, transitionTime*4 + 2*delay);
	}


	function cycle(data) {
		//do the main transitions
		var next = index;
		var previous = index-1;
		if (previous == -1) {
			previous = 0;
		}
		var camsNum = data.length;

		//keep track of transitions
		var wait = 0;


		//previous cam go away
		nextDuration = transitionTime;
		gMap.selectAll( "circle#pt" + (previous % camsNum) )
			.transition()		//1
				.duration(transitionTime)
				.attr("fill", inactiveCirc)
				.attr("r", inactiveRad);
		gCams.selectAll( "#vis" + (previous % camsNum) )
			.transition()		//1
				.duration(transitionTime)
				.style("opacity", 0);
		gCams.selectAll( "#stats" + (previous % camsNum) )
			.transition()		//1
				.duration(transitionTime)
				.style("opacity", 0);
		wait += transitionTime;

		//after that, go to next point
		var location = [ data[next % camsNum].LON, data[next % camsNum].LAT ];
		gMap.selectAll( "circle#pt" + (next % camsNum) )
			.transition()		//2
				.delay(wait)	//after transition 1
				.duration(transitionTime)
				.attr("fill", activeCirc)
				.attr("r", activeRad);
		zoomToPoint(location, wait);	//2
		wait += transitionTime;

		//show text while there
		gCams.selectAll("#stats" + (next % camsNum) )
			.transition()	//3
				.delay(wait) 	//after 2
				.duration(transitionTime)
				.style("opacity", 1);
		wait += transitionTime + delay;

		//go back to regular view and show cam
		reset(wait) //4
		gCams.selectAll( "#vis" + (next % camsNum) )
			.transition()	//4
				.delay(wait)	//after 3
				.duration(transitionTime)
				.style("opacity", 1);
		wait += transitionTime + delay;

		//!!!!
		index++;
	}


 	function zoomToPoint(coords, dt) {
		//zoom map to a location
		var point = projectionFunc(coords);
    var scaleFactor = 3;    //how far to zoom

		gMap
			.transition()
				.delay(dt)
				.duration(transitionTime)
				.attr("transform", "translate(" + xScale(50) + "," + yScale(50) + ")scale(" + scaleFactor + ")translate(" + -point[0] + "," + -point[1] + ")")
	}

	function reset(dt) {
		//put map back where it was

		gMap
			.transition()
				.delay(dt)
				.duration(transitionTime)
				.attr("transform", "translate(" + xScale(50) + "," + yScale(50) + ")scale(" + 1 + ")translate(" + -xScale(50) + "," + -yScale(50) + ")")
	}
</script>
</body>
</html>
