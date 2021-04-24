<?php
require_once  'include/commonAdmin.php';
require_once  'uraNavBar.php';

$adminID = $_SESSION["userID"];
?>
<!Doctype html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,shrink-to-fit=no">
    <meta name="description" content="">
    <title>Manage Users</title>

    <!-- Load common.js from scripts folder -->
    <script src="scripts/common.js"></script>

    <!-- Load common.css from scripts folder -->
    <link rel="stylesheet" media="all" href="css/common.css">

    <!-- Material Design (External) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS (External) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- DataTable CSS-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.bootstrap4.min.css" crossorigin="anonymous">

    <!-- Load fonts from google fonts (External) -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Merienda&family=Open+Sans:wght@300;600&family=Satisfy&display=swap" rel="stylesheet">
    <style>
        @media screen and (max-width : 1920px){
            .mobile-navbar{
                display:none;
            }
        }
            
        @media screen and (max-width : 767px){
            .desktop-navbar{
                display:none;
            }
            .mobile-navbar{
                display:inline;
            }
        }

        #manageUsersTable{
            background-color: white;
        }
        .subFont{
            font-size: 35px;
        }
        .mytable>tbody>tr>td, 
        .mytable>tbody>tr>th, 
        .mytable>tfoot>tr>td, 
        .mytable>tfoot>tr>th, 
        .mytable>thead>tr>td, 
        .mytable>thead>tr>th {
            padding: 10px;
        }

        #manageUsersTable_wrapper{
            margin-bottom: 48px;
        }

        td.child{
            text-align: left;
        }

        td { 
            outline: none; 
        } 

        [hidden] {
            display: none !important;
        }
    </style>
    </head>

    <body>
    <input type="hidden" value=<?= $adminID?>  id="getAdminID">

    <div id="main-overlay">
        <div class="spinner-border text-light spinner" role="status"> </div>
    </div>
  
      
    <div class="container mb-5 mt-5" style="background-color:white">

    <div class="row">
        <div class="col border border-secondary p-4 rounded white-bg shadow ">
            <span style="font-size:28px; color: rgb(92, 92, 92)"  class="material-icons">
                supervisor_account
            </span>
            <span style="font-size:28px; color: rgb(92, 92, 92)"  class="material-icons">
                compare_arrows
            </span>
        
            <span style="font-size:28px; color: rgb(92, 92, 92)"  class="material-icons">
                store
            </span>
            <span class="headings">
                Manage Stores Assignment
            </span>
            <hr>
            <table id="manageUsersTable" class="table mytable table-hover table-striped table-bordered dt-responsive nowrap shadow-sm text-center" style="width:100%">
                <thead> 
                    <tr>
                        <th>Store</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody><!-- Data Dynamically Populated --></tbody>
            </table>
        </div>
    </div>
    </div>

        
  
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- Meant for DataTable -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.5/js/responsive.bootstrap4.min.js" crossorigin="anonymous"></script>
    
    <script>

    // Creates the datatable setting its properties
    $('#manageUsersTable').DataTable({
        "pageLength": datableRowsPerPage,
        fixedHeader: {
            header: true,
            footer: false
        },
        // "targets": 'no-sort',
        // "bSort": false,
        "order": [[0,"asc"]],
        "columnDefs": [{ "orderable": false, "targets": [1,2] }]
    } );

    let optionNameOptionTags = `<option value="">Select Store</option>`;
    // optionNameOptionTags += `<option value="NULL">testbreak</option>`;
    async function mainCall(){
        document.getElementById("main-overlay").style.display = "block";
        let response   = await makeRequest(hostname + "/stores/", "GET", "");
        let storesData = JSON.parse(response).data;

        for (x in storesData){
            optionNameOptionTags += `<option value='${storesData[x].store_id}'>${storesData[x].store_name}</option>`
        }

        console.log(optionNameOptionTags)
        retrieveUserData();
    }

    async function retrieveUserData(){
        let adminID = document.getElementById('getAdminID').value;
        let requestParameters = "admin_id="+adminID
        let response = await makeRequestxwwwFormURLEncode(hostname + "/users/users", "POST", requestParameters);

        var listOfUsers = JSON.parse(response)['response'];
        
        var dataTable = $('#manageUsersTable').DataTable();
        dataTable.clear().draw();

        for(user in listOfUsers){
            if(listOfUsers[user]['admin'] != '1'){

                let userID      = listOfUsers[user]['user_id'];

                let userRow     = document.createElement("tr");
                userRow.setAttribute("id", "Row"+userID);

                let store_name  = document.createElement("td");
                let name        = document.createElement("td");
                let email       = document.createElement("td");
                
                
                name.innerText  = listOfUsers[user]['name'];
                email.innerText = listOfUsers[user]['email'];

                let storeName = listOfUsers[user]['store_name'] == null ? "-": listOfUsers[user]['store_name'];

                if(storeName == "-"){
                    store_name.innerHTML =  
                    `<div id="ddlWithCloseIcon${userID}" class="d-flex justify-content-between">

                        <select id="EditDDL${userID}"  onblur="editStoreNamesText('${userID}',this)" onchange="editStoreNamesText('${userID}',this)" class="custom-select w-100" data-live-search="true" >
                            ${optionNameOptionTags}
                        </select>
                        <span id="closeDDL${userID}" onclick="closeDDL('${userID}')" style="font-size:23px; color: rgb(92, 92, 92)"  class="material-icons pointer align-self-center" hidden>
                            close
                        </span>
                    </div>
                    <div id="StoreNameWithEditIcon${userID}" hidden>
                        <span id="Text${userID}">
                            ${storeName}
                        </span>
                        <span onclick="editStoreName('${userID}')"  style="font-size:25px; color: rgb(92, 92, 92)"  class="material-icons float-right pointer">
                                edit
                        </span>
                        <span onclick="delStoreIDFromUser('${userID}')" style="font-size:25px; color: #FF4136"  class="material-icons float-right pointer">
                                delete
                        </span>
                    </div>`;
                }else{
                    store_name.innerHTML =  
                    ` 
                    <div id="StoreNameWithEditIcon${userID}">
                        <span id="Text${userID}">
                            ${storeName}
                        </span>
                        <span onclick="editStoreName('${userID}')" style="font-size:25px; color: rgb(92, 92, 92)"  class="material-icons float-right pointer">
                                edit
                        </span>
                        <span onclick="delStoreIDFromUser('${userID}')" style="font-size:25px; color: #FF4136"  class="material-icons float-right pointer">
                                delete
                        </span>
                    </div>
                    <div id="ddlWithCloseIcon${userID}" class="d-flex justify-content-between" hidden>
                        <select id="EditDDL${userID}" onblur="editStoreNamesText('${userID}',this)" onchange="editStoreNamesText('${userID}',this)" class="custom-select" data-live-search="true">
                            ${optionNameOptionTags}
                        </select>
                        <span id="closeDDL${userID}" onclick="closeDDL('${userID}')" style="font-size:23px; color: rgb(92, 92, 92)"  class="material-icons pointer align-self-center">
                            close
                        </span>
                    </div>`;
                }


                userRow.append(store_name,name,email);
                dataTable.row.add($(userRow)).draw();
                document.getElementById("main-overlay").style.display = "none";
            }
        }

    }

    function editStoreName(userID){
       document.getElementById("StoreNameWithEditIcon"+userID).hidden = true;    

       document.getElementById("EditDDL"+userID).selectedIndex = 0;

        if(document.getElementById("Text"+userID).innerText == "-"){
            document.getElementById("closeDDL" +userID).hidden = true
        }else{
            document.getElementById("closeDDL" +userID).hidden = false
        }

       document.getElementById("ddlWithCloseIcon"+userID).hidden  = false;
    }

    function closeDDL(userID){
       document.getElementById("StoreNameWithEditIcon"+userID).hidden = false;       
       document.getElementById("ddlWithCloseIcon"+userID).hidden  = true;
    }

    async function editStoreNamesText(userID,e){
        let selectedText = e.options[e.selectedIndex].text; //getText from options tag
        let selectedStoreID = e.value;                      //getValue from options tag

        // As long as user selects a store 
        if(selectedStoreID != "" && document.getElementById("Text"+userID).innerText != selectedText){
            let adminID = document.getElementById('getAdminID').value;
            let requestParameters = "admin_id=" +adminID +
                                    "&user_id=" + userID + 
                                    "&store_id=" +selectedStoreID;
            let response = await makeRequestxwwwFormURLEncode(hostname + "/users/update/store_id", "POST", requestParameters);
            if(JSON.parse(response)['response'] == true){
                console.log("success")

                document.getElementById("Text"+userID).innerText = selectedText;

                document.getElementById("Row"+userID).style.backgroundColor = "#75a925"
                document.getElementById("Row"+userID).style.color = "white"

                // After 3 seconds change remove the greenbg and white font colors
                setTimeout(function(){
                document.getElementById("Row"+userID).removeAttribute("style")
                }, 2000);
            }else{
                document.getElementById("Row"+userID).style.backgroundColor = "#FF4136"
                document.getElementById("Row"+userID).style.color = "white"

                setTimeout(function(){
                document.getElementById("Row"+userID).removeAttribute("style")
                }, 2000);
            }

            document.getElementById("StoreNameWithEditIcon"+userID).hidden = false;       
            document.getElementById("ddlWithCloseIcon"+userID).hidden  = true;  
        }                   
    }

    async function delStoreIDFromUser(userID,e){

        let adminID = document.getElementById('getAdminID').value;
        let requestParameters = "admin_id=" +adminID +
                                "&user_id=" + userID + 
                                "&store_id=None";
        let response = await makeRequestxwwwFormURLEncode(hostname + "/users/update/store_id", "POST", requestParameters);
        if(JSON.parse(response)['response'] == true){
            console.log("success")

            document.getElementById("Text"+userID).innerText = '-';
            editStoreName(userID);
            document.getElementById("Row"+userID).style.backgroundColor = "#75a925"
            document.getElementById("Row"+userID).style.color = "white"

            // After 3 seconds change remove the greenbg and white font colors
            setTimeout(function(){
            document.getElementById("Row"+userID).removeAttribute("style")
            }, 2000);
        }else{
            document.getElementById("Row"+userID).style.backgroundColor = "#FF4136"
            document.getElementById("Row"+userID).style.color = "white"

            setTimeout(function(){
            document.getElementById("Row"+userID).removeAttribute("style")
            }, 2000);
            
            document.getElementById("StoreNameWithEditIcon"+userID).hidden = false;       
            document.getElementById("ddlWithCloseIcon"+userID).hidden  = true;
        }
    }

    mainCall();
    
    </script>
  </body>
</html>