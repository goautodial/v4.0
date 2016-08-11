/*
* campaign id
*/
function load_campaign_name(){
   $.ajax({
     url: "./php/APIs/API_GetHopperLeadsWarning.php",
     cache: false,
     success: function(data){
        $("#refresh_campaign_name").html(data);
     } 
   });
}

