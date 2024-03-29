<?php  
require_once  '../../include/commonAdmin.php';
?>
<!Doctype html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>URA Dashboard</title>

    <!-- ========================== -->
    <!--            CDN             -->
    <!-- ========================== -->
    <!-- Material Design (External) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS (External) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Load d3.js (External) -->
    <script src="https://d3js.org/d3.v4.min.js"></script>

    <!-- Load fonts from google fonts (External) -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Merienda&family=Open+Sans:wght@300;600&family=Satisfy&display=swap" rel="stylesheet">

    <!-- Load SmoothScroll for mobile/tablet browsers (External) -->
    <script src="https://cdn.jsdelivr.net/npm/seamless-scroll-polyfill@1.0.0/dist/es5/seamless.auto-polyfill.min.js"
    data-seamless></script>

    <!-- Load Leaflet from CDN (External) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

    <!-- Load Esri Leaflet from CDN (External) -->
    <script src="https://unpkg.com/esri-leaflet@3.0.1/dist/esri-leaflet.js" integrity="sha512-JmpptMCcCg+Rd6x0Dbg6w+mmyzs1M7chHCd9W8HPovnImG2nLAQWn3yltwxXRM7WjKKFFHOAKjjF2SC4CgiFBg==" crossorigin=""></script>


    <!-- ========================== -->
    <!--        SCRIPTS FOLDER      -->
    <!-- ========================== -->
    <script src="../../scripts/client_URA/leaflet.zoomhome.min.js"></script>

    <!-- Load d3-cloud from scripts folder -->
    <script src="../../scripts/d3.layout.cloud.js"></script>

    <!-- Load selectSubzoneMap from scripts folder -->
    <script src="../../scripts/client_URA/ura_selectSubzoneMap.js" defer></script>

    <!-- Load overallSentimentScore from scripts folder -->
    <script src="../../scripts/overallSentimentScore.js" defer></script>

    <!-- Load ranking from scripts folder -->
    <script src="../../scripts/client_URA/ura_rankingStores.js" defer></script>

    <!-- Load wordcloud from scripts folder -->
    <script src="../../scripts/client_URA/ura_wordCloud.js" defer></script>

    <!-- Load sentimentScoreOverTime from scripts folder -->
    <script src="../../scripts/client_URA/ura_sentimentOverTime.js" defer></script>


    <!-- ========================== -->
    <!--          COMMON CSS        -->
    <!-- ========================== -->
    <!-- Load common.css from scripts folder -->
    <link rel="stylesheet" media="all" href="../../css/common.css">
    <style>
      .header-bg{
        background-color: #d2d2d2;
      }

      .compareHeadings{
        font-family: 'Merienda', cursive;
        color: rgb(68 68 68);
        font-size:20px;
      }

      .compareHeadingIcons{
        color: rgb(68 68 68);
      }


      .collapse-arrow-color{
        color: white;
      }

      .collapse-arrow-color-headers{
        /* font-size:20px; */
        color: rgb(68 68 68);
      }

      .collapse-arrow-bg{
        background-color: #404040;
      }

      .totalNoOfReviewsContainer {
        font-family:'Anton', sans-serif;
        color: rgb(92, 92, 92);
        font-size: 1.8em;
        float:left;
        margin-left:25px;
      }

      .subzoneNameContainer {
        font-family:'Anton', sans-serif;
        color: rgb(92, 92, 92);
        font-size: 1.8em;
        float:left;
        margin-left:25px;
      }

      .rankedStars{
        font-size:22px; 
        color: #fdcc0d;
      }

      /* Overrides list-group-item from Bootstrap */ 
      .list-group-item {
        padding: 7px 10px;
        background-color:rgba(0,0,0,.03)
      }

      .whiteBackground{
        background-image: url("../../images/white-bg.png");
        height:430px;
      }

      .miniScrollReview{
        height:300px;
        overflow-y: scroll
      }

      .scrollStoreRanks{
        height:420px;
        overflow-y: scroll;
      }

      .scrollSubzones{
        height:520px;
        overflow-y: scroll;
      }

      .showStoreNameSubzone, .showStoreNameSubzone1, .showStoreNameSubzone2{
        /* font-size:15px; */
        color: #0275d8;
      }

      .reviewHeaderFont{
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
      }

      .reviewBodyFont{
        font-family: 'Open Sans', sans-serif;
        font-weight: 300;
      }

      .list-group .list-group-item:hover {
        background-color: #0275d8;
        color:white !important;
      }

      .list-group .list-group-item.active {
        background-color: #0275d8;
        border: 1px solid black;
        color:white;
        z-index:0;
      }

      /* STORE RANK CARDS */
      .card .card-header:hover {
        background-color: #0275d8;
        color:white;
        border: 1px solid black;
      }

      .card-active {
        border: 1.5px solid black;
        background-color: #0275d8;
        color:white;
        z-index:0;
      }

      .card-body{
        padding:0.7rem;
      }

      /* MAP RELATED CSS */
      /* TanjongPagarMap */
      #tanjongPagarMap { 
        height:570px;
        /* width:70%; */
        z-index:0;
      }

      /* Zoom Reset Zoom for Map */
      .leaflet-control-zoomhome a {
        font: bold 18px "Lucida Console",Monaco,monospace;
      }

      a.leaflet-control-zoomhome-in,
      a.leaflet-control-zoomhome-out {
        font-size: 1.5em;
        line-height: 26px;
      }
    </style>
  </head>

  <body>
  <!-- NavBar -->
  <?php require_once  'uraNavBar.php' ?>

  <div id="main-overlay">
    <div class="spinner-border text-light spinner" role="status"> </div>
  </div>
  
  <main class="container" style="z-index: 0">
    <div class="container mb-5 mt-3">

      <!-- ============= -->
      <!--     FILTER    -->
      <!-- ============= -->
      <div id = "filterSubZoneContainer">
        <!-- ROW 0 -->
        <div id="subzoneExpandDownArrowRow"  class="row">
          <div  onclick="closeDownRow()" class="col pointer border border-secondary mb-1 rounded collapse-arrow-bg shadow text-center" data-toggle="collapse" aria-expanded="true" data-target="#collapsibleSubzoneFilter">
              <span class="material-icons collapse-arrow-color">
                  keyboard_arrow_down
              </span>
          </div>
        </div>
        <!-- ROW 1 -->
        <div class="row collapse" id="collapsibleSubzoneFilter">
          <!-- ====================== -->
          <!--    LIST GROUP FILTER   -->
          <!-- ====================== -->
          <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
              <div class="lead">
                <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
                <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                  filter_alt
                </span>
                <!-- Create a div where the total number of reviews will be -->
                <div class="float-left ml-1 headings" >Select Subzone(s): </div> 
                <!-- Info PopOver -->
                <a tabindex="0" class="float-right popoverzindex" 
                    title="Select Subzone" 
                    data-placement="left" 
                    data-toggle="popover" 
                    data-trigger="hover focus" 
                    data-content="Select sub zone(s) of interest in Tanjong Pagar<br>
                    <ul>
                    <li>Select 1 subzone to see the sentiments for that area. (Normal mode)</li>
                    <li>Select 2 suzones to compare the sentiments for two areas. (Comparison mode.)</li>
                    <li>Maximum 2 selections at any one time.</li>">
                  <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                    info_outline
                  </span>    
                </a>
              </div>
              <br>
              <hr style="margin-bottom:0px">
              <span id="maxTwoSelections" class="ml-1 text-danger float-left reviewBodyFont">Sorry Max 2 selections only</span>
              <span id="noSelectionError" class="ml-1 text-danger float-left reviewBodyFont">You need to select somthing!</span>
              <div class="reviewBodyFont mt-4" >
                <div id="subzonesList" class="list-group  scrollSubzones list-group-hover">
                  <!-- Subzones get populated here -->
                </div>
                <button id="subZoneSearch" onclick="subZonesSelected()" class="btn btn-primary w-100" type="submit">Search</button>
              </div>
          </div>

          <!-- ====================== -->
          <!--       MAP FILTER       -->
          <!-- ====================== -->
          <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
              <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                map
              </span>
              <!-- Create a div where the total number of reviews will be -->
              <div class="float-left ml-1 headings" >Tanjong Pagar Precinct: </div> 
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex" 
                  title="Total Reviews" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Select sub zone(s) of interest in Tanjong Pagar">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
            <br>
            <hr>
            <div style="width:100%">
              <div id="tanjongPagarMap"></div>
            </div>
          </div>
        </div>
        <!-- ROW 2 -->
        <div class="row" id="subzoneExpandUpArrowRow">
          <div onclick="closeUpRow()" class="col pointer border border-secondary rounded mb-1 collapse-arrow-bg shadow text-center" data-toggle="collapse" aria-expanded="true" data-target="#collapsibleSubzoneFilter">
              <span class="material-icons collapse-arrow-color">
                  keyboard_arrow_up
              </span>
          </div>
        </div>
      </div>

      <!-- ============= -->
      <!--  SINGLE MODE  -->
      <!-- ============= -->
      <div id="SubzoneInformationContainerSingleMode">
        <!-- ROW 3 -->
        <div class="row">
          <!-- ====================== -->
          <!--        SUB-ZONES       -->
          <!-- ====================== -->
          <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
              <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                place
              </span>
              <!-- Create a div where the total number of reviews will be -->
              <div class="float-left ml-1 headings" >Subzone: </div> 
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex" 
                  title="Subzone selected" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Detailed information from the selected subzone">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
              <div id="subzoneNameContainerSubzone" class="container subzoneNameContainer" >
                <!-- Subzone name get filled here -->
              </div>
            </div>
          </div>
          <!-- ========================= -->   
          <!--  OVERALL SENTIMENT SCORE  -->
          <!-- ========================= -->
          <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                  auto_awesome
              </span>
              <div class="float-left ml-1 headings" >Overall Sentiment Score:</div> 
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Overall Sentiment Score" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Higher the number of stars,the more positive customers feel about the stores in this subzone">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
              <div id="overallSentimentScoreSubzone" class="container float-left ml-3"></div>
            </div>
            <!-- Spinner that only shows when loading -->
            <div id="overallSentimentScoreContainerSpinnerSubzone" class="spinner-border text-secondary float-left spinner" role="status" ></div>
          </div>
          <!-- ====================== -->
          <!--     TOTAL REVIEWS      -->
          <!-- ====================== -->
          <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
              <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                rate_review
              </span>
              <!-- Create a div where the total number of reviews will be -->
              <div class="float-left ml-1 headings" >Total Reviews:</div> 
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex" 
                  title="Total Reviews" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Total number of reviews retrieved from TripAdvisor and Google Reviews for this subzone">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
              <div id="totalNoOfReviewsContainerSubzone" class="container totalNoOfReviewsContainer"></div>
            </div>
            <!-- Spinner that only shows when loading -->
            <div id="totalReviewsContainerSpinnerSubzone" class="spinner-border text-secondary float-left spinner" role="status" ></div>
          </div>
        </div>
    
        <!-- ROW 4 -->
        <div class="row">
          <!-- ========================= -->   
          <!--        STORES RANKED      -->
          <!-- ========================= -->
          <div class="col-lg-5 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow block pl-1" >
            <div class="lead">
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              align_horizontal_left
              </span>
              <div class="float-left ml-1 headings" >Stores Ranked by Sentiments:</div> 
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Stores Ranked" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Stores are ranked based on overall sentiment score, with the ones with higher positive sentiment ranked higher. 
                  <br> By selecting a store name, the Sentiment Over Time Bar Chart and Word Cloud will be updated for that store. 
                  To unselect click on the store name again.">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
              <!-- Create a div where the sentimentScore will be -->
              <!-- <div class="container float-left" id="sentimentScoreContainer"></div> -->
            </div>
            <br>
            <hr>
            <div id="storeRanksSubzone" class="scrollStoreRanks float-left w-100">
              <!-- The sorted stores poulate here -->
            </div>

            <!-- White background to the empty space of div when spinner is loading -->
            <div class="whiteBackground"></div>

            <!-- Spinner that only shows when loading -->
            <div class="spinner-border text-secondary float-left spinner" id="storeRanksSpinnerSubzone" role="status" ></div>
          </div>

          <!-- ================================ -->   
          <!--    SENTIMENT OVER TIME CHART     -->
          <!-- ================================ -->
          <div id="entireSentimentOverTimeContainerSubzone" class="col-lg-7 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                timeline
              </span>
              <div class="float-left ml-1 headings" >Sentiment Over Time:</div> 
              <!-- Store name -->
              <div class="float-left ml-1 headings showStoreNameSubzone"></div>
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Sentiment Over Time" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="View the changing sentiments over the different months in a year, with proportion of positive, negative and neutral reviews shown in each month
                    <ul>
                      <li>Change the year via the dropdown list to see the sentiments for that particular year.</li>
                      <li>Check the sort checkbox to sort the months from the most to the least number of reviews.</li>
                      <li>Hovering over each stacked bar, shows the number of reviews for that particular sentiment.</li>
                      <li>Clicking on a particular stacked bar updates the wordcloud based on that sentiment.</li>
                    </ul>">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
              <br>
              <hr>
              <!-- <hr> -->
              <!-- <div class="col-12 border"></div> -->
              <div id="sentimentOverTimeContainerDivSubzone">
                <div class="ml-3 reviewBodyFont" style="font-size: 17px;"> 
                  Select year: <select id="yearSubzone"></select>
    
                  <input type="checkbox" id="sortSubzone" class="ml-1">	Sort
                </div>

                <svg id="sentimentOverTimeStackedBarChartSubzone" class="container" width="1000" height="400">
                  <!-- SOT chart populates here -->
                </svg>
              </div>
              <!-- White background to the empty space of div when spinner is loading -->
              <!-- <div class="whiteBackground"></div> -->

              <!-- Spinner that only shows when loading -->
              <div id="SOTSpinnerSubzone" class="spinner-border text-secondary float-left spinner"  role="status" ></div>
          </div>

          <!-- =================================== -->   
          <!--   STORES WITH LESS THAN # REVIEWS   -->
          <!-- =================================== -->
          <div id="unableToShowInsightsContainerSubzone" class="col-lg-7 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                running_with_errors
              </span>
              <div class="float-left ml-1 headings">Error Obtaining Insights: </div> 
              <!-- Store name -->
              <div class="float-left ml-1 headings showStoreNameSubzone"></div>
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Sentiment Over Time" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="View the changing sentiments over the different months in a year, with proportion of positive, negative and neutral reviews shown in each month
                    <ul>
                      <li>Change the year via the dropdown list to see the sentiments for that particular year.</li>
                      <li>Check the sort checkbox to sort the months from the most to the least number of reviews.</li>
                      <li>Hovering over each stacked bar, shows the number of reviews for that particular sentiment.</li>
                      <li>Clicking on a particular stacked bar updates the wordcloud based on that sentiment.</li>
                    </ul>">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
            <br>
            <hr>
            <div class='p-2 text-center mt-1'>
              <div style="font-size:50px; color:#fa4646" class="material-icons text-center">
                running_with_errors  
              </div>
              <br>
              <span id="unableToDisplayMessage">
                Ooops
                <span id="shopNameForErrorInsightsSubzone"></span> 
                has less than 
                <u><span id="limitForErrorInsights"></span> reviews.</u> 
                <br>
                Let's just view the 
                <span class="noOfReviewsForErrorInsightsSubzone"></span> 
                reviews below.
              </span>
            </div>
     
            <div id="showReviewsContainerForErrorInsightsSubzone" class="miniScrollReview"></div>
             <!-- Spinner that only shows when loading -->
            <div class="spinner-border text-secondary float-left spinner" id="errorInsightsSpinnerSubzone" role="status" ></div>
           
          </div>

        </div>

        <!-- ROW 5 -->
        <div class="row">
          <!-- ========================= -->   
          <!--        WORD CLOUD         -->
          <!-- ========================= -->
          <div id="entireWordCloudContainerSubzone" class="col border border-secondary p-2 rounded mb-2 white-bg shadow" >
            <div class="lead">
              <i style="color: rgb(92, 92, 92)" class="fas fa-cloud fa-lg"></i>
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                cloud 
              </span>
              <div class="float-left ml-1 headings">Word Cloud:</div> 
              <!-- Store name -->
              <div class="float-left ml-1 headings showStoreNameSubzone"></div>
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Word Cloud" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Displays the most frequent nouns with their most frequent adjectives(descriptors).
                    <ul>
                      <li>Nouns are the bigger words on the right</li>
                      <li>Adjectives(Descriptors) are the smaller words on the left</li>
                      <li>Clicking on the adjectives(descriptors), allow users to view the review(s) with those noun and adjectives pairs</li>
                    </ul>
                    ">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
            <!-- Create a div where the wordcloud will be -->
            <div id="wordCloudContainerSubzone" class="container float-left" style="border: 1px solid rgb(36, 36, 36);border-radius: 7px;">
              <!-- Noun Adj's displayed in here -->
            </div>

            <!-- White background to the empty space of div when spinner is loading -->
            <div class="whiteBackground"></div>
            
            <!-- The yellow exclamaintion mark triangle along with the text  -->
            <div id="wordCloudNotEnoughWordsWarningSubzone">
              <!-- values populated in sentimentScoreDonut -->
            </div>
            
            <!-- Spinner that only shows when loading -->
            <div class="spinner-border text-secondary float-left spinner" id="wordCloudContainerSpinnerSubzone" role="status" ></div>
          </div>
        </div>

        <!-- ROW 6 -->
        <div id="wordCloudReviewsContainerSubzone" class="row">
          <!-- ================================ -->   
          <!--        REVIEWS CONTAINER         -->
          <!-- ================================ -->
          <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow" >
            
            <div class="d-flex justify-content-between">
              <div >
                <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                  rate_review
                </span>
                <span class="lead float-left headings">
                  Selected Reviews:
                </span>
                <!-- Store name -->
                <div class="float-left ml-1 headings showStoreNameSubzone"></div>
                <!-- Legend for Noun Adj Highlted colors -->
                <div id="displayLegendSubzone" class="float-left reviewBodyFont">
                  <span class="ml-3 highLightedAdj float-left">Adj</span>
                  <span class="ml-3 highLightedNoun float-left">Noun</span>
                </div>
              </div>

              <span style="font-size:33px; color: rgb(92, 92, 92)" 
                    onclick="(function(){document.getElementById('wordCloudReviewsContainerSubzone').style.display = 'none'})()"
                    class="material-icons mr-2 pointer">
              close
              </span>
            </div>

              <hr>
                <!-- <div class="col-12 border"></div> -->
                <div id="wordCloudClickedReviewsSubzone" class="scrollReviews">
                <!-- Reviews gets Populated Here -->
                </div>
                <hr>
          </div>
        </div>
      </div>

      <!-- ============== -->
      <!--  COMPARE MODE  -->
      <!-- ============== -->
      <div id="SubzoneInformationContainerCompareMode">
        <!-- ROW 3 NAME OSS REVIEWS -->
        <div class="row">
        <!-- ====================== -->
        <!--        SUB-ZONE 1      -->
        <!-- ====================== -->
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow text-center">
            <div class="lead">
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons">
                place
            </span>
            <!-- Create a div where the total number of reviews will be -->
            <span class="headings" >Subzone: </span> 
            <!-- Info PopOver -->
            <a tabindex="0" class="float-right popoverzindex" 
                title="Subzone General Information" 
                data-placement="left" 
                data-toggle="popover" 
                data-trigger="hover focus" 
                data-content="
                <ul>
                <li>Subzone selected</li>
                <li>Overall Sentiment Score: Higher the number of stars,the more positive customers feel about the stores in this subzone</li>
                <li>Total Number of reviews: from TripAdvisor and Google Reivews</li>
                </ul>">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                info_outline
                </span>    
            </a>
            
            <div id="subzoneNameContainerSubzone1" class="container subzoneNameContainer">
                <!-- Subzone name get filled here -->
            </div>
            </div>
            <br>
            <!-- =========================== -->   
            <!--  OVERALL SENTIMENT SCORE 1  -->
            <!-- =========================== -->
            <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons">
                auto_awesome
            </span>
            <span class="headings" >Overall Sentiment Score:</span> 
            
            <div id="overallSentimentScoreSubzone1" class="container float-left ml-3" ></div>
            <!-- Spinner that only shows when loading -->
            <div class="spinner-border text-secondary float-left spinner" id="overallSentimentScoreContainerSpinnerSubzone1" role="status" ></div>
            </div>
            <br>
            <!-- ====================== -->
            <!--     TOTAL REVIEWS 1    -->
            <!-- ====================== -->
            <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons">
                rate_review
            </span>
            <!-- Create a div where the total number of reviews will be -->
            <span class="headings" >Total Reviews:</span> 
            
            <div id="totalNoOfReviewsContainerSubzone1" class="container totalNoOfReviewsContainer"></div>
            <!-- Spinner that only shows when loading -->
            <div id="totalReviewsContainerSpinnerSubzone1" class="spinner-border text-secondary float-left spinner" role="status" ></div>
            </div> 
        </div>
    
        <!-- ====================== -->
        <!--        SUB-ZONE 2      -->
        <!-- ====================== -->
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow text-center">
            <div class="lead">
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons">
                place
            </span>
            <!-- Create a div where the total number of reviews will be -->
            <span class="headings" >Subzone: </span> 
            <!-- Info PopOver -->
            <a tabindex="0" class="float-right popoverzindex" 
                title="Subzone General Information" 
                data-placement="left" 
                data-toggle="popover" 
                data-trigger="hover focus" 
                data-content="
                <ul>
                <li>Subzone selected</li>
                <li>Overall Sentiment Score: Higher the number of stars,the more positive customers feel about the stores in this subzone</li>
                <li>Total Number of reviews: from TripAdvisor and Google Reivews</li>
                </ul>">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                info_outline
                </span>    
            </a>
            
            <div id="subzoneNameContainerSubzone2" class="container subzoneNameContainer" >
                <!-- Subzone name get filled here -->
            </div>
            </div>
            <br>
            <!-- =========================== -->   
            <!--  OVERALL SENTIMENT SCORE 2  -->
            <!-- =========================== -->
            <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons">
                auto_awesome
            </span>
            <span class="headings" >Overall Sentiment Score:</span> 
            
            <div id="overallSentimentScoreSubzone2" class="container float-left ml-3"></div>
            <!-- Spinner that only shows when loading -->
            <div class="spinner-border text-secondary float-left spinner" id="overallSentimentScoreContainerSpinnerSubzone2" role="status" ></div>
            </div>
            <br>
            <!-- ====================== -->
            <!--     TOTAL REVIEWS 2    -->
            <!-- ====================== -->
            <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons">
                rate_review
            </span>
            <!-- Create a div where the total number of reviews will be -->
            <span class="headings" >Total Reviews:</span> 
            
            <div id="totalNoOfReviewsContainerSubzone2" class="container totalNoOfReviewsContainer"></div>
            <!-- Spinner that only shows when loading -->
            <div id="totalReviewsContainerSpinnerSubzone2" class="spinner-border text-secondary float-left spinner" role="status" ></div>
            </div> 
        </div>
        </div>

        <!-- RANKING HEADER -->
        <div class="row">
          <div class="col pointer border border-secondary p-2 mt-2 rounded header-bg shadow" data-toggle="collapse" aria-expanded="true" data-target="#collapsibleRankingContainer">
            <div class="lead text-center">
              <span class="material-icons float-left collapse-arrow-color-headers">
                keyboard_arrow_down
              </span>
              <span style="font-size:30px;" class="material-icons compareHeadingIcons">
              align_horizontal_left
              </span>
              <span class="compareHeadings" >Stores Ranked by Sentiments:
              </span> 
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Stores Ranked" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Stores are ranked based on overall sentiment score, with the ones with higher positive sentiment ranked higher. 
                  <br> By selecting a store name, the Sentiment Over Time Bar Chart and Word Cloud will be updated for that store. 
                  To unselect click on the store name again.">
                <span style="font-size:25px" class="material-icons compareHeadingIcons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
          </div>
        </div>
        <!-- ROW 4 RANKING -->
        <div class="row collapse show" id="collapsibleRankingContainer">
          <!-- ========================= -->   
          <!--      STORES RANKED 1      -->
          <!-- ========================= -->
          <div class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow block pl-1" >
            <!-- Subzone Name -->
            <div class="text-center headings subzoneNameContainerSubzone1"></div>
            <!-- StorenName -->
            <div class="text-center headings showStoreNameSubzone1"></div>
            <hr style="margin-top:0px">

            <div id="storeRanksSubzone1" class="scrollStoreRanks float-left w-100">
              <!-- The sorted stores poulate here -->
            </div>

            <!-- White background to the empty space of div when spinner is loading -->
            <div class="whiteBackground"></div>

            <!-- Spinner that only shows when loading -->
            <div id="storeRanksSpinnerSubzone1" class="spinner-border text-secondary float-left spinner"  role="status" ></div>
          </div>

          <!-- ========================= -->   
          <!--      STORES RANKED 2      -->
          <!-- ========================= -->
          <div class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow block pl-1" >
            <!-- Subzone Name -->
            <div class="text-center headings subzoneNameContainerSubzone2"></div>
            <!-- StorenName -->
            <div class="text-center headings showStoreNameSubzone2"></div>
            <hr style="margin-top:0px">

            <div id="storeRanksSubzone2" class="scrollStoreRanks float-left w-100">
              <!-- The sorted stores poulate here -->
            </div>

            <!-- White background to the empty space of div when spinner is loading -->
            <div class="whiteBackground"></div>

            <!-- Spinner that only shows when loading -->
            <div id="storeRanksSpinnerSubzone2" class="spinner-border text-secondary float-left spinner" role="status" ></div>
          </div>
        </div>


        <!-- SOT HEADER -->
        <div class="row">
          <div class="col pointer border border-secondary p-2 mt-2 rounded header-bg shadow" data-toggle="collapse" aria-expanded="true" data-target="#collapsibleSOTandUnableToDisplayContainer">
            <div class="lead text-center">
              <span class="material-icons float-left collapse-arrow-color-headers">
                keyboard_arrow_down
              </span>
              <span style="font-size:30px" class="material-icons compareHeadingIcons">
                timeline
              </span>
              <span class="compareHeadings" >Sentiment Over Time:</span> 
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Sentiment Over Time" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="View the changing sentiments over the different months in a year, with proportion of positive, negative and neutral reviews shown in each month
                    <ul>
                      <li>Change the year via the dropdown list to see the sentiments for that particular year.</li>
                      <li>Check the sort checkbox to sort the months from the most to the least number of reviews.</li>
                      <li>Hovering over each stacked bar, shows the number of reviews for that particular sentiment.</li>
                      <li>Clicking on a particular stacked bar updates the wordcloud based on that sentiment.</li>
                    </ul>">
                <span style="font-size:25px" class="material-icons compareHeadingIcons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
          </div>
        </div>
        <!-- ROW 5 SOT & UNABLE TO DISPLAY -->
        <div class="row collapse show" id="collapsibleSOTandUnableToDisplayContainer">
          <!-- ================================ -->   
          <!--    SENTIMENT OVER TIME CHART 1   -->
          <!-- ================================ -->
          <div id="entireSentimentOverTimeContainerSubzone1" class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
              <!-- Subzone Name -->
              <div class="text-center headings subzoneNameContainerSubzone1"></div>
              <!-- StorenName -->
              <div class="text-center headings showStoreNameSubzone1"></div>
              <hr style="margin-top:0px">

              <div id="sentimentOverTimeContainerDivSubzone1">
                <div class="ml-3 reviewBodyFont" style="font-size: 17px;"> 
                  Select year: <select id="yearSubzone1"></select>
    
                  <input type="checkbox" id="sortSubzone1" class="ml-1">	Sort
                </div>
                
                <svg id="sentimentOverTimeStackedBarChartSubzone1" class="container" width="1000" height="400">
                  <!-- SOT chart populates here -->
                </svg>
              </div>
              <!-- White background to the empty space of div when spinner is loading -->
              <!-- <div class="whiteBackground"></div> -->

              <!-- Spinner that only shows when loading -->
              <div id="SOTSpinnerSubzone1" class="spinner-border text-secondary float-left spinner"  role="status" ></div>
          </div>

          <!-- ===================================== -->   
          <!--   STORES WITH LESS THAN # REVIEWS 1   -->
          <!-- ===================================== -->
          <div id="unableToShowInsightsContainerSubzone1" class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                running_with_errors
              </span>
              <div class="float-left ml-1 headings">Error Obtaining Insights: </div> 
              <!-- Store name -->
              <div class="float-left ml-1 headings showStoreNameSubzone1"></div>
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Sentiment Over Time" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="View the changing sentiments over the different months in a year, with proportion of positive, negative and neutral reviews shown in each month
                    <ul>
                      <li>Change the year via the dropdown list to see the sentiments for that particular year.</li>
                      <li>Check the sort checkbox to sort the months from the most to the least number of reviews.</li>
                      <li>Hovering over each stacked bar, shows the number of reviews for that particular sentiment.</li>
                      <li>Clicking on a particular stacked bar updates the wordcloud based on that sentiment.</li>
                    </ul>">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
            <br>
            <hr>
            <div class='p-2 text-center mt-1'>
              <div style="font-size:50px; color:#fa4646" class="material-icons text-center">
                running_with_errors  
              </div>
              <br>
              <span id="unableToDisplayMessage">
                Ooops
                <span id="shopNameForErrorInsightsSubzone1"></span> 
                has less than 
                <u><span id="limitForErrorInsights"></span> reviews.</u> 
                <br>
                Let's just view the 
                <span class="noOfReviewsForErrorInsightsSubzone1"></span> 
                reviews below.
              </span>
            </div>
     
            <div id="showReviewsContainerForErrorInsightsSubzone1" class="miniScrollReview"></div>
             <!-- Spinner that only shows when loading -->
            <div id="errorInsightsSpinnerSubzone1" class="spinner-border text-secondary float-left spinner" role="status" ></div>
           
          </div>


          <!-- ================================ -->   
          <!--    SENTIMENT OVER TIME CHART 2   -->
          <!-- ================================ -->
          <div id="entireSentimentOverTimeContainerSubzone2" class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <!-- Subzone Name -->
            <div class="text-center headings subzoneNameContainerSubzone2 "></div>
            <!-- StorenName -->
            <div class="text-center headings showStoreNameSubzone2"></div>
            <hr style="margin-top:0px">
        
            <div id="sentimentOverTimeContainerDivSubzone2">
              <div class="ml-3 reviewBodyFont" style="font-size: 17px;"> 
                Select year: <select id="yearSubzone2"></select>
  
                <input type="checkbox" id="sortSubzone2" class="ml-1">	Sort
              </div>

              <svg id="sentimentOverTimeStackedBarChartSubzone2" class="container" width="1000" height="400">
                <!-- SOT chart populates here -->
              </svg>
            </div>
            <!-- White background to the empty space of div when spinner is loading -->
            <!-- <div class="whiteBackground"></div> -->

            <!-- Spinner that only shows when loading -->
            <div id="SOTSpinnerSubzone2" class="spinner-border text-secondary float-left spinner"  role="status" ></div>
          </div>

          <!-- ===================================== -->   
          <!--   STORES WITH LESS THAN # REVIEWS 2   -->
          <!-- ===================================== -->
          <div id="unableToShowInsightsContainerSubzone2" class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                running_with_errors
              </span>
              <div class="float-left ml-1 headings">Error Obtaining Insights: </div> 
              <!-- Store name -->
              <div class="float-left ml-1 headings showStoreNameSubzone2"></div>
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Sentiment Over Time" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="View the changing sentiments over the different months in a year, with proportion of positive, negative and neutral reviews shown in each month
                    <ul>
                      <li>Change the year via the dropdown list to see the sentiments for that particular year.</li>
                      <li>Check the sort checkbox to sort the months from the most to the least number of reviews.</li>
                      <li>Hovering over each stacked bar, shows the number of reviews for that particular sentiment.</li>
                      <li>Clicking on a particular stacked bar updates the wordcloud based on that sentiment.</li>
                    </ul>">
                <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
            <br>
            <hr>
            <div class='p-2 text-center mt-1'>
              <div style="font-size:50px; color:#fa4646" class="material-icons text-center">
                running_with_errors  
              </div>
              <br>
              <span id="unableToDisplayMessage">
                Ooops
                <span id="shopNameForErrorInsightsSubzone2"></span> 
                has less than 
                <u><span id="limitForErrorInsights"></span> reviews.</u> 
                <br>
                Let's just view the 
                <span class="noOfReviewsForErrorInsightsSubzone2"></span> 
                reviews below.
              </span>
            </div>
     
            <div id="showReviewsContainerForErrorInsightsSubzone2" class="miniScrollReview"></div>
             <!-- Spinner that only shows when loading -->
            <div id="errorInsightsSpinnerSubzone2" class="spinner-border text-secondary float-left spinner" role="status" ></div>
           
          </div>



        </div>

        <!-- WORDCLOUD HEADER -->
        <div id="wordCloudHeader" class="row">
          <div class="col pointer border border-secondary p-2 mt-2 rounded header-bg shadow" data-toggle="collapse" aria-expanded="true" data-target="#collapsibleWordCloudContainer">
            <div class="lead text-center">
              <span class="material-icons float-left collapse-arrow-color-headers">
                keyboard_arrow_down
              </span>
              <span style="font-size:30px;" class="material-icons compareHeadingIcons">
                cloud 
              </span>
              <span class="compareHeadings">Word Cloud:</span> 
              
              <!-- Info PopOver -->
              <a tabindex="0" class="float-right popoverzindex"  
                  title="Word Cloud" 
                  data-placement="left" 
                  data-toggle="popover" 
                  data-trigger="hover focus" 
                  data-content="Displays the most frequent nouns with their most frequent adjectives(descriptors).
                    <ul>
                      <li>Nouns are the bigger words on the right</li>
                      <li>Adjectives(Descriptors) are the smaller words on the left</li>
                      <li>Clicking on the adjectives(descriptors), allow users to view the review(s) with those noun and adjectives pairs</li>
                    </ul>
                    ">
                <span style="font-size:25px" class="material-icons compareHeadingIcons float-right pointer">
                  info_outline
                </span>    
              </a>
            </div>
          </div>
        </div>
        <!-- ROW 6 WordCloud -->
        <div class="row collapse show" id="collapsibleWordCloudContainer">
          <!-- ========================= -->   
          <!--        WORD CLOUD 1       -->
          <!-- ========================= -->
          <div id="entireWordCloudContainerSubzone1" class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow" >
            <!-- Subzone Name -->
            <div class="text-center headings subzoneNameContainerSubzone1"></div>
            <!-- Store name -->
            <div class="text-center headings showStoreNameSubzone1"></div>
            <hr style="margin-top:0px;margin-bottom: 5px;">
            
            <!-- Create a div where the wordcloud will be -->
            <div id="wordCloudContainerSubzone1" class="container float-left" style="border: 1px solid rgb(36, 36, 36);border-radius: 7px;">
              <!-- Noun Adj's displayed in here -->
            </div>

            <!-- White background to the empty space of div when spinner is loading -->
            <div class="whiteBackground"></div>
            
            <!-- The yellow exclamaintion mark triangle along with the text  -->
            <div id="wordCloudNotEnoughWordsWarningSubzone1">
              <!-- values populated in sentimentScoreDonut -->
            </div>
            
            <!-- Spinner that only shows when loading -->
            <div id="wordCloudContainerSpinnerSubzone1" class="spinner-border text-secondary float-left spinner" role="status" ></div>
          </div>
          <!-- ========================= -->   
          <!--        WORD CLOUD 2       -->
          <!-- ========================= -->
          <div id="entireWordCloudContainerSubzone2" class="ml-auto col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow" >
            <!-- Subzone Name -->
            <div class="text-center headings subzoneNameContainerSubzone2"></div>
            <!-- Store name -->
            <div class="text-center headings showStoreNameSubzone2"></div>
            <hr style="margin-top:0px;margin-bottom: 5px;">

            <!-- Create a div where the wordcloud will be -->
            <div id="wordCloudContainerSubzone2" class="container float-left" style="border: 1px solid rgb(36, 36, 36);border-radius: 7px;">
              <!-- Noun Adj's displayed in here -->
            </div>

            <!-- White background to the empty space of div when spinner is loading -->
            <div class="whiteBackground"></div>
            
            <!-- The yellow exclamaintion mark triangle along with the text  -->
            <div id="wordCloudNotEnoughWordsWarningSubzone2">
              <!-- values populated in sentimentScoreDonut -->
            </div>
            
            <!-- Spinner that only shows when loading -->
            <div id="wordCloudContainerSpinnerSubzone2" class="spinner-border text-secondary float-left spinner" role="status" ></div>
          </div>
        </div>


        <!-- REVIEWS CONTAINER HEADER -->
        <div id="reviewsContainer">
          <div class="row">
            <div class="col border border-secondary p-2 mt-2 rounded header-bg shadow">
              <div class="lead text-center">
                  <span style="font-size:33px;" class="material-icons compareHeadingIcons">
                    rate_review
                  </span>
                  <span class="compareHeadings">Selected Reviews:</span>
                  <!-- Legend for Noun Adj Highlted colors -->
                  <span id="displayLegend" class=" reviewBodyFont">
                    <span class="ml-3 highLightedAdj ">Adj</span>
                    <span class="ml-3 highLightedNoun ">Noun</span>
                  </span>
                  <span style="font-size:33p" 
                      onclick="(closeAndClearReviews())"
                      class="float-right compareHeadingIcons material-icons pointer">
                close
                </span>
              </div>
            </div>
          </div>
          <!-- ROW 7 Reviews Container -->
          <div class="row">
            <!-- ================================ -->   
            <!--        REVIEWS CONTAINER 1       -->
            <!-- ================================ -->
            <div id="wordCloudReviewsContainerSubzone1" class="col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow" >    
               <!-- Subzone Name -->
              <div class="text-center headings subzoneNameContainerSubzone1"></div>
              <!-- Store name -->
              <div class="text-center headings showStoreNameSubzone1"></div>
              <hr style="margin-top:0px;margin-bottom: 5px;">

              <!-- <div class="col-12 border"></div> -->
              <div id="wordCloudClickedReviewsSubzone1" class="scrollReviews">
                <!-- Reviews gets Populated Here -->
              </div>
              <hr>
            </div>
            <!-- ================================ -->   
            <!--        REVIEWS CONTAINER 2       -->
            <!-- ================================ -->
            <div id="wordCloudReviewsContainerSubzone2" class="ml-auto col-lg-6 col-md-12 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow" >    
              <!-- Subzone Name -->
              <div class="text-center headings subzoneNameContainerSubzone2"></div>
              <!-- Store name -->
              <div class="text-center headings showStoreNameSubzone2"></div>
              <hr style="margin-top:0px;margin-bottom: 5px;">

              <!-- <div class="col-12 border"></div> -->
              <div id="wordCloudClickedReviewsSubzone2" class="scrollReviews">
                <!-- Reviews gets Populated Here -->
              </div>
              <hr>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================== -->   
      <!--   DONT CLICK NOUN MODAL FOR BOTH MODES   -->
      <!-- ======================================== -->
      <div class="modal fade" id="displayNounAdjModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-md" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Ooops. Let's try that again.</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
                  </div>
                  <div class="modal-body">
                      <h6>
                          Please click on the <span style="color:#659c34">Adjectives</span> instead of <span style="color:#e22401">Nouns</span>.
                      </h6>
                      <img src="../../images/nounAdjWarningPrompt.png" width='100%' height="auto">
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Oh Okay!</button>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </main>

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <!-- Load common.js from scripts folder -->
  <script src="../../scripts/common.js"></script>

  <script>
    selectedReviewSubzone='';
    selectedReviewSubzone1='';
    selectedReviewSubzone2='';

    document.getElementById("subzoneExpandUpArrowRow").style.display                = "none";
    document.getElementById("subzoneExpandDownArrowRow").style.display              = "none";
    
    document.getElementById("subZoneSearch").style.display                          = "none";
    document.getElementById("wordCloudReviewsContainerSubzone").style.display       = "none";

    document.getElementById("SubzoneInformationContainerSingleMode").style.display  = "none";
    document.getElementById("SubzoneInformationContainerCompareMode").style.display = "none";

    document.getElementById("maxTwoSelections").style.display                       = "none";
    document.getElementById("noSelectionError").style.display                       = "none";


    let reviewsSubzone;
    let reviewsSubzone1;
    let reviewsSubzone2;

    let nounAdjPairsSubzone;
    let nounAdjPairsSubzone1;
    let nounAdjPairsSubzone2;

    let nounAdjPairsIndividualStoreSubzone;
    let nounAdjPairsIndividualStoreSubzone1;
    let nounAdjPairsIndividualStoreSubzone2;
    let isCallForSubZone = true;
    // The main call to retrieve values for all charts other than wordcloud
    async function getSentimentScore(subzone_ID,subzoneChoice){
      document.getElementById("main-overlay").style.display = "block";
      let reviewsResponse  = await makeRequest(hostname + "/reviews/road/"+subzone_ID, "GET", "");
      window["reviews"+subzoneChoice] = JSON.parse(reviewsResponse).data;
      
      dataPrepOnPageLoad(window["reviews"+subzoneChoice],true,subzoneChoice);
      prepareSentimentOverTime(window["reviews"+subzoneChoice],subzoneChoice);
      document.getElementById("main-overlay").style.display = "none";
    }

    let sentimentDataForWordCloud = [];
    function dataPrepOnPageLoad(reviews, processOSTAndTotalReviews, subzoneChoice){

      // Sentiments pos,neg,neu
      let pos  = [];
      let neg  = [];
      let neu  = [];

      //Overall Sentiment Score Accumulator
      let totalCompoundScores = 0;

      for(x in reviews){
        totalCompoundScores += reviews[x].compound_score;
      }
 
      // Only process if for subzoneLevel
      if(processOSTAndTotalReviews){
        // Total Reviews Number
        let subZoneTotalReviews = reviews.length;
        document.getElementById("totalNoOfReviewsContainer"+subzoneChoice).textContent = subZoneTotalReviews;

        //Overall Sentiment Score 
        let subZoneOverallSentimentScore = totalCompoundScores/subZoneTotalReviews;
        displayStars(subZoneOverallSentimentScore, false, subzoneChoice)
      }

      // Preparing data for adj clicks to see review
      refactorResponseForReviewsViaWordCloudAdjs(reviews,subzoneChoice);
    }  

    refactoredResponseSubzone = {};
    refactoredResponseSubzone1 = {};
    refactoredResponseSubzone2 = {};
    function refactorResponseForReviewsViaWordCloudAdjs(response,subzoneChoice){
      // console.log(window["refactoredResponse"+subzoneChoice]);
      /* Prepares a "search table" for the noun adj word cloud pairs. When user selects any adj,
       its reviewid can be used to do a search in O(1) using key value pairs rather than O(n^2) using nested for loops
       From this: {
                    compound_score: xxx,
                    review_date   : xxx,
                    review_id     : xxx,
                    review_text   : xxx,
                    store_id      : xxx
                    }
       To this: review_id :{
                    compound_score: xxx,
                    review_date   : xxx,
                    review_text   : xxx,
                    store_id      : xxx
                    }
      */
      for (x in response){
        window["refactoredResponse"+subzoneChoice][response[x].review_id] = {
                            compound_score : response[x].compound_score,
                            review_text    : response[x].review_text,
                            review_date    : response[x].review_date,
                            store_id       : response[x].store_id
                          }
                              
      }
      // console.log(window["refactoredResponse"+subzoneChoice])
    }


    function enlargePolygon(e){
      // console.log(e.id)
      // Remove all whitespaces
      let subzone = e.id.replace(/\s/g, "")
      // Make first character lowercase
      subzone = subzone.charAt(0).toLowerCase() + subzone.slice(1);

      if(!selectedSubZones.includes(e.id)){
        window[subzone].setStyle({weight: 3,color: 'red'})
        //Unselect from list here
      }
    }

    function shrinkPolygon(e){
      // console.log(e.id)
      // Remove all whitespaces
      let subzone = e.id.replace(/\s/g, "")
      // Make first character lowercase
      subzone = subzone.charAt(0).toLowerCase() + subzone.slice(1);

      if(!selectedSubZones.includes(e.id)){
        window[subzone].setStyle({weight: 1.5,color: '#3388ff'})
        //Unselect from list here
      }
    }

    function selectSubzone(e){

      // Remove all whitespaces
      let subzone = e.id.replace(/\s/g, "")
      // Make first character lowercase
      subzone = subzone.charAt(0).toLowerCase() + subzone.slice(1);

      if(selectedSubZones.includes(e.id)){
        document.getElementById("noSelectionError").style.display = "none";
        document.getElementById("maxTwoSelections").style.display = "none";
        window[subzone].setStyle({weight: 1.5,color: '#3388ff'})

        const index = selectedSubZones.indexOf(e.id)
        if (index > -1) {
            selectedSubZones.splice(index, 1);
            e.classList.remove("active");
        }
      }
      else{
        if(selectedSubZones.length < 2){
          document.getElementById("noSelectionError").style.display = "none";
          document.getElementById("maxTwoSelections").style.display = "none";
          window[subzone].setStyle({weight: 3,color: 'red'})
          selectedSubZones.unshift(e.id)
          e.classList.add("active");
        }else{
          document.getElementById("maxTwoSelections").style.display = "block";
        }        
      }
      // console.log(selectedSubZones)
      if(selectedSubZones.length == 0){
        document.getElementById("subZoneSearch").style.display  = "none";
      }else{
        document.getElementById("subZoneSearch").style.display  = "block";
      }
    }



    function closeUpRow(){
      document.getElementById("subzoneExpandUpArrowRow").style.display   = "none";
      document.getElementById("subzoneExpandDownArrowRow").style.display = "block";
    }

    function closeDownRow(){
      document.getElementById("subzoneExpandDownArrowRow").style.display = "none";
      document.getElementById("subzoneExpandUpArrowRow").style.display   = "block";
    }
    
    function closeAndClearReviews(){
      document.getElementById('reviewsContainer').style.display                  = 'none';
      document.getElementById('wordCloudReviewsContainerSubzone1').style.display = 'none';
      document.getElementById('wordCloudReviewsContainerSubzone2').style.display = 'none';
      
      document.getElementById("wordCloudClickedReviewsSubzone1").innerHTML = '';
      document.getElementById("wordCloudClickedReviewsSubzone2").innerHTML = '';

    }

    // ===============
    //  Search Button
    // ===============
    function subZonesSelected(){
      // console.log(selectedSubZones);
      document.getElementById("maxTwoSelections").style.display = "none";
      document.getElementById("noSelectionError").style.display = "none";

      // =============
      //   ERROR MODE
      // =============
      if(selectedSubZones.length > 2 || selectedSubZones.length <= 0 || selectedSubZones == undefined){
        // RETURN ERROR MESSAGE ONLY WAY TO REACH HERE IF USER INSPECTS ELEMENT TRYING TO BRICK THE FRONTEND
        document.getElementById("noSelectionError").style.display = "block";
      }
      
      // ==============
      //   SINGLE MODE
      // ==============
      else if(selectedSubZones.length == 1){
        $('#collapsibleSubzoneFilter').collapse("hide");

        function getKeyByValue(object, value) {
          return Object.keys(object).find(key => object[key] === value);
        }

        subzone1 = getKeyByValue(subZoneIdNameDic,selectedSubZones[0]);
        document.getElementById("subzoneNameContainerSubzone").innerText = selectedSubZones[0]

        // Sentiment over time, Overall Sentiment Score(Stars), Total Reviews
        getSentimentScore(subzone1,"Subzone");

        // Wordcloud
        let url = hostname + "/adj_noun_pairs/road/"+subzone1;
        retrieveWordCloudNounAdjPairs(url,"GET","","Subzone");

        // Ranking list
        getRanking(subzone1,"Subzone");

        document.getElementById("SubzoneInformationContainerCompareMode").style.display = "none";
        document.getElementById("SubzoneInformationContainerSingleMode").style.display = "block";
        
        document.getElementById("subzoneExpandUpArrowRow").style.display     = "block";
        document.getElementById("subzoneExpandDownArrowRow").style.display   = "none";

        //Clear StoreNames if present beside headers
        document.querySelectorAll('.showStoreNameSubzone').forEach(function(elem){
          elem.innerText = "";
        });

        //ErrorInsights
        document.getElementById("errorInsightsSpinnerSubzone").style.display                   = "none";
        document.getElementById("unableToShowInsightsContainerSubzone").style.display          = "none";
        document.getElementById("entireSentimentOverTimeContainerSubzone").style.display       = "block";
        document.getElementById("entireWordCloudContainerSubzone").style.display               = "block";

        //Overall Sentiment Score (Stars)
        document.getElementById("overallSentimentScoreSubzone").style.display                  = "none";
        document.getElementById("overallSentimentScoreContainerSpinnerSubzone").style.display  = "block";
        
        //Total Number of Reviews 
        document.getElementById("totalNoOfReviewsContainerSubzone").style.display              = "none";
        document.getElementById("totalReviewsContainerSpinnerSubzone").style.display           = "block";

        //Ranked Stores
        document.getElementById("storeRanksSubzone").style.display                             = "none";
        document.getElementById("storeRanksSpinnerSubzone").style.display                      = "block";

        //Sentiment Over Time Stacked Bar Chart
        document.getElementById("sentimentOverTimeContainerDivSubzone").style.display          = "none";
        document.getElementById("SOTSpinnerSubzone").style.display                             = "block";

        //WordCloud
        document.getElementById("wordCloudContainerSubzone").style.display                     = "none"; 
        document.getElementById("wordCloudContainerSpinnerSubzone").style.display              = "block";
      }
      
      // ==============
      //  COMPARE MODE
      // ==============
      else{
        $('#collapsibleSubzoneFilter').collapse("hide");

        // Open if not already open 
        $('#collapsibleSOTandUnableToDisplayContainer').collapse("show");
        $('#collapsibleWordCloudContainer').collapse("show");
        $('#collapsibleRankingContainer').collapse("show");

        function getKeyByValue(object, value) {
          return Object.keys(object).find(key => object[key] === value);
        }

        subzone1 = getKeyByValue(subZoneIdNameDic,selectedSubZones[1]);
        subzone2 = getKeyByValue(subZoneIdNameDic,selectedSubZones[0]);


        //Subzone1 Name for Main header
        document.getElementById("subzoneNameContainerSubzone1").innerText = selectedSubZones[1];
        //Subzone1 Name for Containers below 
        document.querySelectorAll('.subzoneNameContainerSubzone1').forEach(function(elem){
            elem.innerHTML = selectedSubZones[1];
        });

        //Subzone2 Name for Main header
        document.getElementById("subzoneNameContainerSubzone2").innerText = selectedSubZones[0];
        //Subzone2 Name for Containers below
        document.querySelectorAll('.subzoneNameContainerSubzone2').forEach(function(elem){
            elem.innerHTML = selectedSubZones[0];
        });

        // Sentiment over time1, Overall Sentiment Score(Stars)1, Total Reviews 1
        getSentimentScore(subzone1,"Subzone1");

        // Sentiment over time2, Overall Sentiment Score(Stars)2, Total Reviews 2
        getSentimentScore(subzone2,"Subzone2");

        // Wordcloud 1
        let url1 = hostname + "/adj_noun_pairs/road/"+subzone1;
        retrieveWordCloudNounAdjPairs(url1,"GET","","Subzone1");

        // Wordcloud 2
        let url2 = hostname + "/adj_noun_pairs/road/"+subzone2;
        retrieveWordCloudNounAdjPairs(url2,"GET","","Subzone2");

        // Ranking list 1
        getRanking(subzone1,"Subzone1");

        // Ranking list 2
        getRanking(subzone2,"Subzone2");

        document.getElementById("SubzoneInformationContainerSingleMode").style.display          = "none";
        document.getElementById("SubzoneInformationContainerCompareMode").style.display         = "block";

        document.getElementById("subzoneExpandUpArrowRow").style.display                        = "block";
        document.getElementById("subzoneExpandDownArrowRow").style.display                      = "none";

        //ErrorInsights 1
        document.getElementById("errorInsightsSpinnerSubzone1").style.display                   = "none";
        document.getElementById("unableToShowInsightsContainerSubzone1").style.display          = "none";
        document.getElementById("entireSentimentOverTimeContainerSubzone1").style.display       = "block";
        document.getElementById("entireWordCloudContainerSubzone1").style.display               = "block";
        //ErrorInsights 2
        document.getElementById("errorInsightsSpinnerSubzone2").style.display                   = "none";
        document.getElementById("unableToShowInsightsContainerSubzone2").style.display          = "none";
        document.getElementById("entireSentimentOverTimeContainerSubzone2").style.display       = "block";
        document.getElementById("entireWordCloudContainerSubzone2").style.display               = "block";
        
        //Overall Sentiment Score (Stars) 1
        document.getElementById("overallSentimentScoreSubzone1").style.display                  = "none";
        document.getElementById("overallSentimentScoreContainerSpinnerSubzone1").style.display  = "block";
        //Overall Sentiment Score (Stars) 2
        document.getElementById("overallSentimentScoreSubzone2").style.display                  = "none";
        document.getElementById("overallSentimentScoreContainerSpinnerSubzone2").style.display  = "block";
        
        //Total Number of Reviews 1
        document.getElementById("totalNoOfReviewsContainerSubzone1").style.display              = "none";
        document.getElementById("totalReviewsContainerSpinnerSubzone1").style.display           = "block";
        //Total Number of Reviews 2
        document.getElementById("totalNoOfReviewsContainerSubzone2").style.display              = "none";
        document.getElementById("totalReviewsContainerSpinnerSubzone2").style.display           = "block";

        //Ranked Stores 1
        document.getElementById("storeRanksSubzone1").style.display                             = "none";
        document.getElementById("storeRanksSpinnerSubzone1").style.display                      = "block";
        //Ranked Stores 2
        document.getElementById("storeRanksSubzone2").style.display                             = "none";
        document.getElementById("storeRanksSpinnerSubzone2").style.display                      = "block";

        //Sentiment Over Time Stacked Bar Chart 1
        document.getElementById("sentimentOverTimeContainerDivSubzone1").style.display          = "none";
        document.getElementById("SOTSpinnerSubzone1").style.display                             = "block";
        //Sentiment Over Time Stacked Bar Chart 2
        document.getElementById("sentimentOverTimeContainerDivSubzone2").style.display          = "none";
        document.getElementById("SOTSpinnerSubzone2").style.display                             = "block";

        //WordCloud 1
        document.getElementById("wordCloudContainerSubzone1").style.display                     = "none"; 
        document.getElementById("wordCloudContainerSpinnerSubzone1").style.display              = "block";
        //WordCloud 2
        document.getElementById("wordCloudContainerSubzone2").style.display                     = "none"; 
        document.getElementById("wordCloudContainerSpinnerSubzone2").style.display              = "block";

        //Reviews Container
        document.getElementById('reviewsContainer').style.display                               = 'none';

        //Remove store names beside the headers of Rank, SOT,WordCloud and Reviews Container 1
        document.querySelectorAll('.showStoreNameSubzone1').forEach(function(elem){
            elem.innerHTML = "";
        });
        //Show Subzone Name beside the headers of Rank, SOT,WordCloud and Reviews Container  1
        document.querySelectorAll('.subzoneNameContainerSubzone1').forEach(function(elem){
            elem.style.display = "block";
        });

        //Remove store names beside the headers of Rank, SOT,WordCloud and Reviews Container 2
        document.querySelectorAll('.showStoreNameSubzone2').forEach(function(elem){
            elem.innerHTML = "";
        });
        //Show Subzone Name beside the headers of Rank, SOT,WordCloud and Reviews Container  2
        document.querySelectorAll('.subzoneNameContainerSubzone2').forEach(function(elem){
            elem.style.display = "block";
        });

      }
    }


    var selectedSubZones = [];
    var subZoneIdNameDic = {};
    async function mainCall(){
      document.getElementById("main-overlay").style.display = "block";
      $('#collapsibleSubzoneFilter').collapse("show");

      let subzoneResponse  = await makeRequest(hostname + "/roads/", "GET", "");
      subzones = JSON.parse(subzoneResponse).data;

      // console.log(subzones)

      for (x in subzones){
        subZoneIdNameDic[subzones[x].road_id] = subzones[x].road_name;
        document.getElementById("subzonesList").innerHTML += 
        `<a id="${subzones[x].road_name}" onclick="selectSubzone(this)" onmouseover="enlargePolygon(this)" onmouseout="shrinkPolygon(this)" class="list-group-item pointer reviewBodyFont mr-1">${subzones[x].road_name}</a>`;
      }
      // console.log(subZoneIdNameDic)
      document.getElementById("main-overlay").style.display = "none";
    }

    mainCall();
    </script>

  </body>
</html>