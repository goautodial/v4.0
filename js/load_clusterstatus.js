/*
* server id
*/
function load_server_id(){
   $.ajax({
     type: 'POST',
     url: "./php/APIs/API_GetClusterStatus.php",
     data: { api1: "serverid"},
     cache: false,
     success: function(data){
        $("#refresh_server_id").html(data);
     } 
   });
}

/*
* server ip 
*/
function load_server_ip(){
    $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api2: "serverip"},
        cache: false,
        success: function(data){
           $("#refresh_server_ip").html(data);
    } 
   });
}

/*
* active 
*/
function load_active(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api3: "active"},
        cache: false,
        success: function(data){
           if (data == "Y") {
               //code
               data = "<font color='green'>Active</font>";
           }else{
               data = "<font color='red'><i>Inactive</i></font>"
           }
           $("#refresh_active").html(data);
        }
    });
}

/*
 * system load
*/
function load_sysload(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api4: "sysload"},
        cache: false,
        success: function(data){
           $("#refresh_sysload").html(data);
        }
    });
}

/*
 * cpu load
*/
function load_cpu(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api5: "cpu"},
        cache: false,
        success: function(data){
           $("#refresh_cpu").html(data);
        }
    });
}

/*
 * channel total
*/
function load_channels_total(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api6: "channels"},
        cache: false,
        success: function(data){
           $("#refresh_channels_total").html(data);
        }
    });
}

/*
 * disk usage
*/
function load_disk_usage(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api7: "diskusage"},
        cache: false,
        success: function(data){
           $("#refresh_disk_usage").html(data);
        }
    });
}

/*
 * server time
*/
function load_s_time(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api8: "servertime"},
        cache: false,
        success: function(data){
           $("#refresh_s_time").html(data);
        }
    });
}


/*
 * php time
*/
function load_php_time(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api9: "phptime"},
        cache: false,
        success: function(data){
           $("#refresh_php_time").html(data);
        }
    });
}

/*
 * DB time
*/
function load_db_time(){
   $.ajax({
        type: 'POST',
        url: "./php/APIs/API_GetClusterStatus.php",
        data: { api10: "dbtime"},
        cache: false,
        success: function(data){
           $("#refresh_db_time").html(data);
        }
    });
}