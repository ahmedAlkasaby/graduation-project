<script type="text/javascript">
    $('body').on('change', '.user-type-role', function () {
        var value = $(this).val();
        if(value == "manger"){
            $('.user-all-role').removeClass('hide');
        }else{
            $('.user-all-role').addClass('hide');
        }
    });

    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
         input.attr("type", "text");
        } else {
         input.attr("type", "password");
        }
      });

$(document).ready(function(){
 $("#password").keyup(function(){
  check_pass();
 });
});

function check_pass()
{
 var val=document.getElementById("password").value;
 var meter=document.getElementById("meter");
 var no=0;
 if(val!="")
 {
  // If the password length is less than or equal to 6
  if(val.length<=6)no=1;
  // If the password length is greater than 6 and contain any lowercase alphabet or any number or any special character
  if(val.length>6 && (val.match(/[a-z]/) || val.match(/\d+/) || val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)))no=2;
  // If the password length is greater than 6 and contain alphabet,number,special character respectively
  if(val.length>8 && ((val.match(/[a-z]/) && val.match(/\d+/)) || (val.match(/\d+/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) || (val.match(/[a-z]/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))))no=3;
  // If the password length is greater than 6 and must contain alphabets,numbers and special characters
  if(val.length>10 && val.match(/[a-z]/) && val.match(/\d+/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))no=4;
  if(no==1)
  {
   $("#meter").animate({width:'25%'},200);
   meter.style.backgroundColor="#dd4b39";
   //document.getElementById("pass_type").innerHTML="Very Weak";
  }
  if(no==2)
  {
   $("#meter").animate({width:'50%'},200);
   meter.style.backgroundColor="#f39c12";
   //document.getElementById("pass_type").innerHTML="Weak";
  }
  if(no==3)
  {
   $("#meter").animate({width:'75%'},200);
   meter.style.backgroundColor="#3c8dbc";
   //document.getElementById("pass_type").innerHTML="Good";
  }
  if(no==4)
  {
   $("#meter").animate({width:'100%'},200);
   meter.style.backgroundColor="#00a65a";
   //document.getElementById("pass_type").innerHTML="Strong";
  }
 }
 else
 {
  meter.style.backgroundColor="#e8e8e8";
  //document.getElementById("pass_type").innerHTML="";
 }
}
</script>





