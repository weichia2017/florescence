var map = L.map('tanjongPagarMap', {zoomSnap: 0.10,zoomControl: false}).setView([1.277294, 103.8437922], 16.4);

var zoomHome = L.Control.zoomHome();
zoomHome.addTo(map);

L.esri.basemapLayer('Topographic').addTo(map);



// OVERALL
function onClick(e) {
 
  let subzone = e.target._tooltip._content;
  if(selectedSubZones.includes(subzone)){
    e.target.setStyle({weight: 1.5,color: '#3388ff'})

    const index = selectedSubZones.indexOf(subzone)
    if (index > -1) {
        selectedSubZones.splice(index, 1);
        document.getElementById(subzone).classList.remove("active");
    }
  }
  else{
    if(selectedSubZones.length < 2){
        e.target.setStyle({weight: 3,color: 'red'})
        selectedSubZones.unshift(subzone)
        document.getElementById(subzone).classList.add("active");
    }
    
  }
//   console.log(selectedSubZones)

  if(selectedSubZones.length == 0){
    document.getElementById("subZoneSearch").style.display  = "none";
  }else{
      console.log("hi")
    document.getElementById("subZoneSearch").style.display  = "block";
  }

}

function onMouseOver(e) {
    let subzone = e.target._tooltip._content;
    if(!selectedSubZones.includes(subzone)){
        e.target.setStyle({weight: 3,color: 'red'})
        document.getElementById(subzone).classList.add("active");
    }
}

function onMouseOut(e) {
    let subzone = e.target._tooltip._content;
    if(!selectedSubZones.includes(subzone)){
      e.target.setStyle({weight: 1.5,color: '#3388ff'})
      document.getElementById(subzone).classList.remove("active");
      //Unselect from list here
    }
}

var roadStyle =
{
    // fill: false,
    // fillColor: '#000000',
    // color: 'black',
    fillOpacity:0.15,
    weight: 1.5
};

var tanjongPagarStyle = 
{
    fill:false,
    weight:3,
    color:'black'
}
  
// ==========================
//       TANJONG PAGAR 
// ==========================
var tanjongPagarBoundry = L.polygon([[1.2794784, 103.8396565], [1.277966, 103.8403967], [1.2774619, 103.8381008], [1.2763142, 103.8383797], [1.2752523, 103.8387767], [1.2734024, 103.8386084], [1.2734239, 103.8396706], [1.2733677, 103.8397629], [1.2732872, 103.8398326], [1.273196, 103.8399024], [1.2730781, 103.8399506], [1.2726866, 103.8399399], [1.2728143, 103.8439866], [1.2728036, 103.8448771], [1.2727607, 103.8456174], [1.2750004, 103.8474939], [1.2783684, 103.8496075], [1.2787653, 103.848835], [1.2797265, 103.8471641], [1.2798874, 103.8467242], [1.2802092, 103.8462844], [1.2803001, 103.8463158], [1.2807666, 103.8459349], [1.2804234, 103.8456828], [1.2802572, 103.8454628], [1.280949, 103.8451892], [1.2811903, 103.8449907], [1.2813566, 103.8447815], [1.2805307, 103.8441432], [1.2801928, 103.8434941], [1.2798648, 103.8427709], [1.2807118, 103.8423422], [1.2819936, 103.8416502], [1.2806068, 103.8405236], [1.2800105, 103.8399992], [1.2794903, 103.8396613]],
                          { weight: 3}).addTo(map);
tanjongPagarBoundry.setStyle(tanjongPagarStyle).addTo(map);


// ==========================
//        DUXTON HILL 
// ==========================
var duxtonHill = L.polygon([[1.2797342, 103.8432534], [1.2793427, 103.842607],[1.2792623, 103.8425239], [1.2791845, 103.8425293], [1.2790934, 103.8426017], [1.278844, 103.84232], [1.2786697, 103.8425239], [1.2784431, 103.8423462], [1.2780301, 103.8426198], [1.2779711, 103.8427942], [1.2786308, 103.8430892], [1.2789436, 103.8430569], [1.2789463, 103.8432071], [1.2792895, 103.8431802], [1.2793807, 103.8432822], [1.2794102, 103.8432982], [1.2797347, 103.8432553], [1.2797342, 103.8432534]]).addTo(map);
duxtonHill.on('click', onClick);
duxtonHill.on('mouseover', onMouseOver);
duxtonHill.on('mouseout', onMouseOut);
duxtonHill.setStyle(roadStyle).addTo(map);
duxtonHill.bindTooltip("Duxton Hill");

// ==========================
//        DUXTON ROAD 
// ==========================
var duxtonRoad = L.polygon([[1.2797399, 103.8432756], [1.279882, 103.843525], [1.279705, 103.8435921], [1.2794556, 103.8435867], [1.2794047, 103.8435465], [1.2791955, 103.8435572], [1.2787262, 103.8436618], [1.2777938, 103.8433153], [1.2778287, 103.8430149], [1.2779198, 103.8427762], [1.2786385, 103.8430927], [1.2789469, 103.8430605], [1.2789469, 103.8432134], [1.2792931, 103.8431822], [1.2793763, 103.8432841], [1.2793977, 103.8432921], [1.2794178, 103.8433015], [1.2797367, 103.8432655]]).addTo(map);
duxtonRoad.on('click', onClick);
duxtonRoad.on('mouseover', onMouseOver);
duxtonRoad.on('mouseout', onMouseOut);
duxtonRoad.setStyle(roadStyle).addTo(map);
duxtonRoad.bindTooltip("Duxton Road");  

// ==========================
//     TANJONG PAGAR ROAD 
// ==========================
// INSERT into roads (road_id,road_name) VALUES (3,"Tanjong Pagar Road");
var tanjongPagarRoad = L.polygon([[1.2798982, 103.8435422], [1.2801583, 103.8439445], [1.2802077, 103.8441613], [1.2798269, 103.8444885], [1.2796231, 103.8443276], [1.279253, 103.8442632], [1.2788133, 103.8442257], [1.2787895, 103.844146], [1.2785991, 103.8441084], [1.278575, 103.8441567], [1.2782934, 103.8441004], [1.2779797, 103.8439689], [1.2777164, 103.8439106], [1.2774858, 103.8438677], [1.2772498, 103.8439348], [1.2772521, 103.8436365], [1.2774838, 103.8436541], [1.2775034, 103.8432965], [1.2777828, 103.8433401], [1.2782494, 103.8435171], [1.2787226, 103.843688], [1.2791948, 103.8435801], [1.2793906, 103.843572], [1.2794496, 103.8436176], [1.2796989, 103.8436149], [1.2798935, 103.8435492]])
tanjongPagarRoad.on('click', onClick);
tanjongPagarRoad.on('mouseover', onMouseOver);
tanjongPagarRoad.on('mouseout', onMouseOut);
tanjongPagarRoad.setStyle(roadStyle).addTo(map);
tanjongPagarRoad.bindTooltip("Tanjong Pagar Road");  


// duxtonHill.bindPopup("Duxton Hill");
// duxtonRoad.bindPopup("Duxton Road");
// duxtonRoad.setStyle(duxtonRoadStyle).addTo(map);