<?php  
require_once  '../../include/common.php';

$storeID = $_SESSION["storeID"];
$userName = $_SESSION["name"];
?>
<!Doctype html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flourishing Our Locale</title>

    <!-- ========================== -->
    <!--            CDN             -->
    <!-- ========================== -->
    <!-- Material Design (External) -->
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">

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


    <!-- ========================== -->
    <!--        SCRIPTS FOLDER      -->
    <!-- ========================== -->
    <!-- Load d3-cloud from scripts folder -->
    <script src="../../scripts/d3.layout.cloud.js"></script>

    <!-- Load overallSentimentScore from scripts folder -->
    <script src="../../scripts/overallSentimentScore.js" defer></script>

    <!-- Load wordcloud from scripts folder -->
    <script src="../../scripts/businesses/wordCloud.js" defer></script>

    <!-- Load sentimentScoreDonut from scripts folder -->
    <script src="../../scripts/businesses/sentimentScoreDonut.js" defer></script>

    <!-- Load sentimentScoreOverTime from scripts folder -->
    <script src="../../scripts/businesses/sentimentOverTime.js" defer></script>


    <!-- ========================== -->
    <!--          COMMON CSS        -->
    <!-- ========================== -->
    <!-- Load common.css from scripts folder -->
    <link rel="stylesheet" href="../../css/common.css">

    <style>
      #totalNoOfReviewsContainer {
        font-family:'Anton', sans-serif;
        color: rgb(92, 92, 92);
        font-size: 1.8em;
        float:left;
        margin-left:25px;
      }

      #wordCloudContainer {
        border: 1px solid rgb(36, 36, 36);
        border-radius: 7px;
      }

      #wordCloudWhiteBackground{
        background-image: url("../../images/white-bg.png");
        height:460px;
      }
    </style>
   
  </head>

  <body>
  <!-- NavBar -->
  <?php require_once 'bizNavBar.php'?>
  
  <div id="main-overlay">
    <div class="spinner-border text-light spinner" role="status"> </div>
  </div>

  <main class="container" style="z-index: 0">
    <input type="hidden" value=<?= $storeID?>   id="getStoreID">
    <input type="hidden" value=<?= $userName?>  id="getUserName">

    <div id="accountVerificationMode" class="container mb-5 mt-5">

      <!-- ======================================== -->   
      <!--        ACCOUNT VERIFICATION MODE         -->
      <!-- ======================================== -->
      <div class="row">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <div style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              warning_amber  
            </div>
            <div class="float-left ml-1 headings">Account verification: </div> 
          </div>
          <br>
          <hr>
          <div class='p-2 text-center mt-5'>
            <div style="font-size:50px; color: #fdcc0d;" class="material-icons text-center">
                    warning_amber  
            </div>
            <br>
            <span id="clickEmailLinkForVerification"></span>
             
          </div>
          <br>
          <br>
          </div>
          <hr>
        </div>
      </div>
    </div>

    <div id="unableToDisplayMode" class="container mb-5 mt-5">

      <!-- ======================================== -->   
      <!--   UNABLE TO DISPLAY INSIGHTS CONTAINER   -->
      <!-- ======================================== -->
      <div class="row">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              running_with_errors
            </span>
            <div class="float-left ml-1 headings">Error Obtaining Insights: </div> 
          </div>
          <br>
          <hr>
          <div class='p-2 text-center mt-5'>
            <div style="font-size:50px; color:#fa4646" class="material-icons text-center">
              running_with_errors  
            </div>
            <br>
            <span id="unableToDisplayMessage">
              Sorry we are not able to obtain much insights.. <br>
              <span class="shopName"></span> has 
              <u><span class="noOfReviews"><!-- Number Populates here --></span> reviews</u> 
              from both our available sources, TripAdvisor and GoogleReviews.<br><br>
              Please try to get your customers to leave more reviews :><br>
              However, you can still view the <span class="noOfReviews"><!-- Number Populates here --></span> 
              reviews below.
            </span>
          </div>
          <br>
          <br>
          <div id="showReviewsContainer" class="scrollReviews">
          <!-- Reviews gets Populated Here -->
          </div>
          <hr>
        </div>
      </div>
    </div>
  
    <div id="normalMode" class="container mb-5 mt-5">
      <!-- ROW 1 -->
      <div class="row">
        
        <!-- ========================= -->   
        <!--  OVERALL SENTIMENT SCORE  -->
        <!-- ========================= -->
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons-outlined float-left">
              auto_awesome
            </span>
            <div class="float-left ml-1 headings" >Overall Sentiment Score:</div> 
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
            <div class="float-left ml-1 headings" >Total Reviews:</div> 
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
            <div class="float-left ml-1 headings" >Sentiment Score:</div> 
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
            <div class="float-left ml-1 headings">Word Cloud:</div> 
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
          <div id="wordCloudWhiteBackground"></div>
         
          <!-- The yellow exclamaintion mark triangle along with the text  -->
          <div id="wordCloudNotEnoughWordsWarning">
            <!-- values populated in sentimentScoreDonut -->
          </div>
          
          <!-- Spinner that only shows when loading -->
          <div class="spinner-border text-secondary float-left spinner" id="wordCloudContainerSpinner" role="status" ></div>
        </div>

        <!-- ========================= -->   
        <!--   DONT CLICK NOUN MODAL   -->
        <!-- ========================= -->
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

      <!-- ======================================== -->   
      <!--   WORD CLOUD DISPLAY REVIEWS CONTAINER   -->
      <!-- ======================================== -->
      <div class="row" id="wordCloudReviewsContainer">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          
          <div class="d-flex justify-content-between">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <div >
              <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                rate_review
              </span>
              <span class="float-left ml-1 headings">
                Selected Reviews:
              </span>
              <!-- Legend for Noun Adj Highlted colors -->
              <div id="displayLegend" class="float-left reviewBodyFont">
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
            <div class="float-left ml-1 headings" >Sentiment Over Time:</div> 
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
          <div class="d-flex justify-content-between">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <div >
              <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
                rate_review
              </span>
              <span class="float-left ml-1 headings">
                Selected Reviews:
               </span>
              <!-- Legend for Noun Adj Highlted colors -->
              <!-- <div id="displayLegend" class="float-left">
                <span class="ml-3 highLightedAdj float-left">Adj</span>
                <span class="ml-3 highLightedNoun float-left">Noun</span>
              </div> -->
            </div>

            <span style="font-size:33px; color: rgb(92, 92, 92)" 
                  onclick="(closeAndClearSelectionsInSOT())"
                  class="material-icons mr-2 pointer">
            close
            </span>
          </div>
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
    
  <!-- Load common.js from scripts folder -->
  <script src="../../scripts/common.js"></script>

  <script>    
    selectedReview='';

    document.getElementById("accountVerificationMode").style.display        = "none";
    document.getElementById("unableToDisplayMode").style.display            = "none";
    document.getElementById("normalMode").style.display                     = "none";

    document.getElementById("wordCloudReviewsContainer").style.display      = "none";
    document.getElementById("sentimentReviewsContainer").style.display      = "none";
    document.getElementById("wordCloudContainerSpinner").style.display      = "none";
    document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "none";
      
    let shopID    = document.getElementById('getStoreID').value;
    let usersName = document.getElementById('getUserName').value;    

    function populateReviews(storeBased_Reviews){
      let chosenReviewsWithFullData = [];
      console.log(chosenReviewsWithFullData)
      //Convert String Date to Normal Date for the sorting
      for (x in storeBased_Reviews){
        chosenReviewsWithFullData.push({review_id   : storeBased_Reviews[x]['review_id'],
                                        review_date : new Date(storeBased_Reviews[x]['review_date']),
                                        review_text : storeBased_Reviews[x]['review_text']
                                      })
      }
      // Sort By Reviews By Date
      chosenReviewsWithFullData.sort(function(a,b){
        return new Date(b.review_date) - new Date(a.review_date);
      });

      //Display the values
      for (x in chosenReviewsWithFullData){
        let imageName = "tripAdvisorLogo.png"
        if(chosenReviewsWithFullData[x].review_id.split("-")[1] == 1){
            imageName = "googleMapsLogo.png"
        } 
        document.getElementById("showReviewsContainer").innerHTML +=
                `<div class="card mr-3 ml-3 mt-2">
                    <div class="card-body">
                    <h6 class="reviewHeaderFont">
                      <img src="../../images/${imageName}" width='30px' height="auto">
                      Review Date: ${chosenReviewsWithFullData[x]['review_date'].toLocaleDateString()}
                    </h6>
                    <p class="reviewBodyFont">${chosenReviewsWithFullData[x]['review_text']}</p>
                    </div>
                </div>`;      
      }
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
          pos.push(response[x].review_id);
        }
        else if(response[x].compound_score <= -0.05){
          neg.push(response[x].review_id);
        }
        else{
          neu.push(response[x].review_id);
        }
      }
      sentimentDataForWordCloud.push({Positive :pos,
                                      Negative :neg,
                                      Neutral  :neu});

      //Overall Sentiment Score 
      let overallSentimentScore = totalCompoundScores/totalReviews;
      displayStars(overallSentimentScore)
            
      //Sentiment Score
      prepareSentimentDonut(totalReviews);

      //Preparing data for adj clicks to see review
      refactorResponseForReviewsViaWordCloudAdjs(response);
    }  

    let refactoredResponse = {};
    function refactorResponseForReviewsViaWordCloudAdjs(response){
      // console.log(response);
      /* Prepares a "search table" for the noun adj word cloud pairs. When user selects any adj,
        its reviewid can be used to do a search in O(1) using key value pairs rather than O(n^2) using nested for loops
        From this: {
                      compound_score: xxx,
                      review_date   : xxx,
                      review_id     : xxx,
                      review_text   : xxx
                      }
        To this: review_id :{
                      compound_score: xxx,
                      review_date   : xxx,
                      review_text   : xxx
                      }
      */
      for (x in response){
        refactoredResponse[response[x].review_id] = {
                            compound_score : response[x].compound_score,
                            review_text    : response[x].review_text,
                            review_date    : response[x].review_date 
                          }
                              
      }
      // console.log(refactoredResponse)
    }

    function closeAndClearSelectionsInSOT(){
      // Clear all selected rect in sentimentOverTimeChartSVG
      sentimentOverTimeChartSVG.selectAll("g.layer")
        .selectAll("rect")
        .attr("stroke","pink")
        .attr("stroke-width","0.2");
      
      document.getElementById('sentimentReviewsContainer').style.display = 'none';
    }

    // ========================
    //  SHOW VERIFICATION MODE
    // ========================
    function showVerificationMode(){
      document.getElementById("accountVerificationMode").style.display = "block";
      document.getElementById("unableToDisplayMode").style.display     = "none";
      document.getElementById("normalMode").style.display              = "none"; 

      document.getElementById("clickEmailLinkForVerification").innerHTML += 
      ` Dear ${usersName}, thank you for your interest in our dashboard.<br> 
        Due to privacy issues, we will require some verification so that we can be sure we are sharing the dashboard information to the right person.<br><br>
        Please  <a href=mailto:${emailToSendForVerification}?subject="FlourishingOurLocale_Dashboard_Ownership_Request">click here</a>
        to contact our staff to verify your particulars on your store ownership details.`;

      document.getElementById("main-overlay").style.display = "none";
    }

    // ========================
    //     SHOW NORMAL MODE
    // ========================
    function showNormalMode(storeBased_Reviews){
      document.getElementById("accountVerificationMode").style.display = "none";
      document.getElementById("unableToDisplayMode").style.display     = "none";
      document.getElementById("normalMode").style.display              = "block";

      // Wordcloud
      let wordCloudData  = [];
      let url = hostname + "/adj_noun_pairs/store/" + shopID;
      $(document).ready(retrieveWordCloudNounAdjPairs(url,"GET",""));

      // Donut, Total Reviews, Overall Sentiment Score(Stars)
      dataPrepOnPageLoad(storeBased_Reviews);

      // Sentiments Over Time
      sentimentOverTimePrepareData(storeBased_Reviews); 
    }

    // =======================================
    //  SHOW UNABLE TO DISPLAY INSIGHTS MODE
    // =======================================
    function showUnableToDisplay(storeBased_Reviews){
      document.getElementById("accountVerificationMode").style.display = "none";
      document.getElementById("unableToDisplayMode").style.display     = "block";
      document.getElementById("normalMode").style.display              = "none";
      
      let totalReviews = storeBased_Reviews.length;
      let elements = document.getElementsByClassName('noOfReviews');
      for (index in elements){
          elements[index].innerText = totalReviews;
      }      
      populateReviews(storeBased_Reviews);
    }
    
    async function getStoreName(){
      let storeInfo = await makeRequest(hostname + "/stores/" + shopID, "GET", "");
      let elements = document.getElementsByClassName('shopName');
      let shopName = JSON.parse(storeInfo).data[0].store_name;
      for (index in elements){
          elements[index].innerText = shopName
      }
      sessionStorage.setItem('shopName', shopName);
    }

    // The main call to retrieve values for all charts other than wordcloud
    let sentimentDataForWordCloud = [];
    async function mainCall(){
      document.getElementById("main-overlay").style.display = "block";

      // console.log(shopID);
      if(shopID == 'null'){
        // ========================
        //  SHOW VERIFICATION MODE
        // ========================
        showVerificationMode();
        return;
      }

      getStoreName();
      let response = await makeRequest(hostname + "/reviews/store/" + shopID, "GET", "");
      let storeBased_Reviews = JSON.parse(response).data;

      if(storeBased_Reviews.length > limitNormalMode){
        // ========================
        //     SHOW NORMAL MODE
        // ========================
        showNormalMode(storeBased_Reviews);
      }else{
        // =======================================
        //  SHOW UNABLE TO DISPLAY INSIGHTS MODE
        // =======================================
        showUnableToDisplay(storeBased_Reviews);
      }
      document.getElementById("main-overlay").style.display = "none";
    }

    mainCall();
  </script>
  </body>
</html>