<?php  
$storeID = '1';
if( isset($_GET['storeID']) ){
  $storeID = $_GET['storeID'];
  if($storeID == '')
    $storeID = '1';
}
?>
<!Doctype html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Flourishing Our Locale</title>

    <!-- Fontwesome -->
    <!-- <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/> -->

    <!-- Material Design (External) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS (External) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Load d3.js (External) -->
    <script src="https://d3js.org/d3.v4.min.js"></script>

    <!-- Load Anton font from google fonts (External) -->
    <!-- https://fonts.google.com/specimen/Anton?preview.text_type=custom&sidebar.open=true&selection.family=Anton -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">

    <!-- Load SmoothScroll for mobile/tablet browsers (External) -->
    <!-- <script src="scripts/seamless.auto-polyfill.min.js" data-seamless></script> -->
    <script src="https://cdn.jsdelivr.net/npm/seamless-scroll-polyfill@1.0.0/dist/es5/seamless.auto-polyfill.min.js"
    data-seamless></script>

    <!-- Load d3-cloud from scripts folder -->
    <script src="scripts/d3.layout.cloud.js"></script>

    <!-- Load wordcloud from scripts folder -->
    <script src="scripts/overallSentimentScore.js" defer></script>

    <!-- Load wordcloud from scripts folder -->
    <script src="scripts/wordCloud.js" defer></script>

    <!-- Load sentimentScore from scripts folder -->
    <script src="scripts/sentimentScoreDonut.js" defer></script>

    <!-- Load sentimentScoreOverTime from scripts folder -->
    <script src="scripts/sentimentOverTime.js" defer></script>

    <style>
      .white-bg{
        background-color: white;
      }

      #main-overlay {
        height: 100%;
        width: 100%;
        display: none;
        position: fixed;
        z-index: 2;
        top: 0;
        left: 0;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0, 0.9);
      }

      .navbar-brand {
        padding-top: .75rem;
        padding-bottom: .75rem;
        font-size: 1rem;
        background-color: rgba(0, 0, 0, .25);
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
      }

      .navbar .navbar-toggler {
        top: .25rem;
        right: 1rem;
      }

      body{
        background-color: rgb(243, 243, 243);
      }

      #totalNoOfReviewsContainer {
        font-family:'Anton', sans-serif;
        color: rgb(92, 92, 92);
        font-size: 1.8em;
        float:left;
        margin-left:25px;
      }

      .stars{
        font-size:40px; 
        color: #fdcc0d;
      }

      #wordCloudContainer {
        border: 1px solid rgb(36, 36, 36);
        border-radius: 7px;
      }

      .spinner{
        position: absolute;
        top: 50%;
        left: 47%;
      }

      .wordCloudWhiteBackground{
        background-image: url("images/white-bg.png");
        height:400px;
      }

      .scrollReviews{
        height:400px;
        overflow-y: scroll;
      }

      .pointer{
        cursor: pointer;
      }

      .highLightedNoun{
        /* Olive */
        background-color: #3D9970;
        padding: 0.1em 0.1em;
      }

      .highLightedAdj{
        /* Teal */
        padding: 0.1em 0.1em;
        background-color: #39CCCC;
      }

    </style>
  </head>

  <body>
  <div id="main-overlay">
    <div class="spinner-border text-light spinner" role="status"> </div>
  </div>

  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow" style="z-index: 1">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#"><span id="shopNameNavBar"></span></a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </header>

  <main class="container" style="z-index: 0">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
      <!-- <button class="btn btn-primary" id="UpdateButton" onclick=test()>Update</button> -->
    </div>

    <!-- <form>
      <input type="text" name=storeID>
      <input type="submit">
    </form> -->
    <input type="hidden" value=<?= $storeID?>  id="getStoreID">

    <div class="container mb-5">
      <!-- ROW 1 -->
      <div class="row">
        <!-- ====================== -->
        <!--     TOTAL REVIEWS      -->
        <!-- ====================== -->
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
              rate_review
            </span>
            <!-- Create a div where the total number of reviews will be -->
            <div class="float-left ml-1" >Total Reviews:</div> 
            <!-- Info PopOver -->
            <a tabindex="0" class="float-right popoverzindex" 
                title="Total Reviews" 
                data-placement="left" 
                data-toggle="popover" 
                data-trigger="hover focus" 
                data-content="Total number of reviews retrieved from TripAdvisor and Google Reviews.">
              <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                info_outline
              </span>    
            </a>
            <div class="container" id="totalNoOfReviewsContainer"></div>
          </div>
        </div>
        <!-- ========================= -->   
        <!--  OVERALL SENTIMENT SCORE  -->
        <!-- ========================= -->
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              auto_graph
            </span>
            <div class="float-left ml-1" >Overall Sentiment Score:</div> 
            <!-- Info PopOver -->
            <a tabindex="0" class="float-right popoverzindex"  
                title="Overall Sentiment Score" 
                data-placement="left" 
                data-toggle="popover" 
                data-trigger="hover focus" 
                data-content="Higher the number of stars,the more positive customers feel about the store.">
              <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                info_outline
              </span>    
            </a>
            <div class="container float-left ml-3" id="overallSentimentScore"></div>
          </div>
        </div>
      </div>

      <!-- ROW 2 -->
      <div class="row">
        <!-- ========================= -->   
        <!--   SENTIMENT SCORE DONUT   -->
        <!-- ========================= -->
        <div class="col-lg-5 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow block pl-1" >
          <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              tag_faces
            </span>
            <div class="float-left ml-1" >Sentiment Score:</div> 
            <!-- Info PopOver -->
            <a tabindex="0" class="float-right popoverzindex"  
                title="Sentiment Score" 
                data-placement="left" 
                data-toggle="popover" 
                data-trigger="hover focus" 
                data-content="Proportion of positive, negative and neutral reviews (in terms of percent).
                <ul>
                  <li>Hovering over the donut chart will display the number of reviews for each sentiment </li>
                  <li>Clicking on the donut(E.g. Positive), brings up the most frequent noun-adjectives pairs for those reviews (E.g.positive reviews).</li>
                </ul>">
              <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                info_outline
              </span>    
            </a>
            <!-- Create a div where the sentimentScore will be -->
            <div class="container float-left" id="sentimentScoreContainer"></div>
          </div>
        </div>

        <!-- ========================= -->   
        <!--        WORD CLOUD         -->
        <!-- ========================= -->
        <div class="col-lg-7 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow" >
          <div class="lead">
            <i style="color: rgb(92, 92, 92)" class="fas fa-cloud fa-lg"></i>
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              cloud 
            </span>
            <div class="float-left ml-1">Word Cloud:</div> 
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
          <div class="container float-left" id="wordCloudContainer">
            <!-- Noun Adj's displayed in here -->
          </div>

          <!-- White background to the empty space of div when spinner is loading -->
          <div class="wordCloudWhiteBackground"></div>
         
          <!-- The yellow exclamaintion mark triangle along with the text  -->
          <div id="wordCloudNotEnoughWordsWarning">
            <!-- values populated in sentimentScoreDonut -->
          </div>
          
          <!-- Spinner that only shows when loading -->
          <div class="spinner-border text-secondary float-left spinner" id="wordCloudContainerSpinner" role="status" ></div>
        </div>
      </div>

      <!-- ================================ -->   
      <!--   WORD CLOUD REVIEWS CONTAINER   -->
      <!-- ================================ -->
      <div class="row" id="wordCloudReviewsContainer">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          
          <div class="d-flex justify-content-between">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <div >
              <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                rate_review
              </span>
              <span class="lead float-left">
                Selected Reviews:
               </span>
              <!-- Legend for Noun Adj Highlted colors -->
              <div id="displayLegend" class="float-left">
                <span class="ml-3 highLightedAdj float-left">Adj</span>
                <span class="ml-3 highLightedNoun float-left">Noun</span>
              </div>
            </div>

            <span style="font-size:33px; color: rgb(92, 92, 92)" 
                  onclick="(function(){document.getElementById('wordCloudReviewsContainer').style.display = 'none'})()"
                  class="material-icons mr-2 pointer">
            close
            </span>
          </div>

            <hr>
              <!-- <div class="col-12 border"></div> -->
              <div id="wordCloudClickedReviews" class="scrollReviews">
              <!-- Reviews gets Populated Here -->
              </div>
              <hr>
        </div>
      </div>

      <!-- ROW 3 -->
      <div class="row">
        <!-- ================================ -->   
        <!--    SENTIMENT OVER TIME CHART     -->
        <!-- ================================ -->
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              timeline
            </span>
            <div class="float-left ml-1" >Sentiment Over Time:</div> 
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
                    <li>Clicking on a particular stacked bar shows the review(s) for that sentiment on that particular month.</li>
                  </ul>">
              <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-right pointer">
                info_outline
              </span>    
            </a>
            <br>
            <hr>
            <div class="ml-3"> 
              Select year: <select id="year"></select>

              <input type="checkbox" id="sort" class="ml-1">	Sort
            </div>
            <!-- <hr> -->
            <!-- <div class="col-12 border"></div> -->
            <div id="sentimentOverTimeContainerDiv">
              <svg class="container" id="sentimentOverTimeContainer" width="1000" height="400">
                <!-- SOT chart populates here -->
              </svg>
            </div>
          </div>
        </div>
      </div>
      <!-- ================================ -->   
      <!--      SOT REVIEWS CONTAINER       -->
      <!-- ================================ -->
      <div class="row" id="sentimentReviewsContainer">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
              rate_review
            </span>
            <span style="font-size:33px; color: rgb(92, 92, 92)" 
                  onclick="(function(){document.getElementById('sentimentReviewsContainer').style.display = 'none'})()"
                  class="material-icons float-right mr-2 pointer">
            close
            </span>
            <div>Selected Reviews:</div> 
            <hr>
            <!-- <div class="col-12 border"></div> -->
            <div id="sentimentOverTimeClickedReviews" class="scrollReviews">
            <!-- Reviews gets Populated Here -->
            </div>
            <hr>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    

  <script>

    var hostname = "http://35.175.55.18:5000";
    selectedReview='';

    // Popover
    $(document).ready(function(){
      $('[data-toggle="popover"]').popover({ 
        html : true, 
        content: function() {
          return $('#popover_content_wrapper').html();
        }
      });   
    });
  
    // function test(){
    //  console.log("hi")
    // }

    document.getElementById("wordCloudReviewsContainer").style.display      = "none";
    document.getElementById("sentimentReviewsContainer").style.display      = "none";
    document.getElementById("wordCloudContainer").style.display             = "block";
    document.getElementById("wordCloudContainerSpinner").style.display      = "none";
    document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "none";
    
    let storeIDByUser = document.getElementById('getStoreID').value;
    let shopID = (storeIDByUser == null) ? '1' : storeIDByUser;
    
    function makeRequest(url,method,values) {
      return new Promise(function (resolve, reject) {

        let request = new XMLHttpRequest();

        request.open(method, url);
        request.timeout = 20000;
        request.onload = function () {
            if (this.status >= 200 && this.status < 300) {
                resolve(request.response);
            } else {
                reject({
                    status: this.status,
                    statusText: request.statusText
                });
            }
        };
        request.onerror = function () {
            reject({
                status: this.status,
                statusText: request.statusText
            });
        };
        // request.setRequestHeader('Authorization', 'Bearer ' + token)
        request.setRequestHeader("Content-type", "application/JSON");
        request.setRequestHeader('Access-Control-Allow-Origin', '*');
        // request.withCredentials = false;
        request.send(values);
      });
    }

    // The main call to retrieve values for all charts other than wordcloud
    let sentimentDataForWordCloud = [];
    async function getSentimentScore(){
      document.getElementById("main-overlay").style.display = "block";
      let adjNounPairs = await makeRequest(hostname + "/reviews/" + shopID, "GET", "");
      let response     = JSON.parse(adjNounPairs).data;

      // console.log(response);

      dataPrepOnPageLoad(response);
      sentimentOverTimePrepareData(response);
      document.getElementById("main-overlay").style.display = "none";
    }

    function dataPrepOnPageLoad(response){
      // Total Reviews Number
      let totalReviews = response.length;
      document.getElementById("totalNoOfReviewsContainer").textContent = totalReviews;

      // Sentiment Donut pos,neg,neu
      let pos          = [];
      let neg          = [];
      let neu          = [];

      //Overall Sentiment Score Accumulator
      let totalCompoundScores = 0;

      for(x in response){
        totalCompoundScores += response[x].compound_score;

        if(response[x].compound_score >= 0.05){
          pos.push({review_id   : response[x].review_id,
                    review_text : response[x].review_text,
                    review_date : response[x].review_date
                  });
        }
        else if(response[x].compound_score <= -0.05){
          neg.push({review_id   : response[x].review_id,
                    review_text : response[x].review_text,
                    review_date : response[x].review_date
                  });
        }
        else{
          neu.push({review_id   : response[x].review_id,
                    review_text : response[x].review_text,
                    review_date : response[x].review_date
                  });
        }
      }
      sentimentDataForWordCloud.push({Positive :pos,
                                      Negative :neg,
                                      Neutral  :neu});

      console.log(sentimentDataForWordCloud);


      // dataToBeSentToServer[0].data.push(...sentimentDataForWordCloud[0]['Negative'], 
      //                                   ...sentimentDataForWordCloud[0]['Neutral'],
      //                                   ...sentimentDataForWordCloud[0]['Positive'])


      //Overall Sentiment Score 
      let overallSentimentScore = totalCompoundScores/totalReviews;
      displayStars(overallSentimentScore)
            
      //Sentiment Score
      prepareSentimentDonut(totalReviews);

      //Preparing data for adj clicks to see review
      refactorResponseForReviewsViaWordCloudAdjs(response);
    }  

    /* Prepares a "search table" for the noun adj word cloud pairs. When user selects any adj,
       its reviewid can be used to do a search in O(1) using key value pairs rather than O(n^2) using nested for loops
       From this: {
                    compound_score: xxx,
                    review_data   : xxx,
                    review_id     : xxx,
                    review_text   : xxx
                    }
       To this: review_id :{
                    compound_score: xxx,
                    review_data   : xxx,
                    review_text   : xxx
                    }
    */
    let refactoredResponse = {};
    function refactorResponseForReviewsViaWordCloudAdjs(response){
      // console.log(response);
      for (x in response){
        refactoredResponse[response[x].review_id] = {
                            compound_score : response[x].compound_score,
                            review_text    : response[x].review_text,
                            review_date    : response[x].review_date 
                          }
                              
      }
      // console.log(refactoredResponse)
    }

    //Temporary till we have user Login Feature
    async function getStoreName(){
      let storeInfo = await makeRequest(hostname + "/stores/" + shopID, "GET", "");
      document.getElementById('shopNameNavBar').innerText = JSON.parse(storeInfo).data[0].store_name;
    }

    getStoreName();
    getSentimentScore();
  </script>

  
  </body>
</html>