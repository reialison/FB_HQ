<?php
session_start();


$your_email ='';// <<=== update to your email address



$errors = '';
$name = '';
$visitor_email = '';
$phone = '';
$subject1 = '';
$user_message = '';

if(isset($_POST['submit']))
{
	$name = $_POST['name'];
	$visitor_email = $_POST['email'];
		$phone = $_POST['phone'];
	$subject1 = $_POST['subject1'];
	$user_message = $_POST['message'];
	
		//send the email
		$to = $your_email;
		$subject="Kingpainting Contact Us enquiry";
		$from = $visitor_email;
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		
	 	$body = "A user  $name submitted the contact form:\n\n".
		"Name: $name\n\n".
		"Email: $visitor_email \n\n".
		"Phone Number: $phone\n\n".
		"Selected Subject: $subject1\n\n".
		"Message: $user_message\n\n".
		
		
		$headers = "From: $from \r\n";
		$headers .= "Reply-To: $visitor_email \r\n";
		
		if(mail($to, $subject, $body,$headers)){
		
		?><script>
		window.location = 'http://www.kingpainting.com.au/thanku.php';
		</script>
		<?php
		}

}

// Function to validate against any email injection attempts
function IsInjected($str)
{
  $injections = array('(\n+)',
              '(\r+)',
              '(\t+)',
              '(%0A+)',
              '(%0D+)',
              '(%08+)',
              '(%09+)'
              );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str))
    {
    return true;
  }
  else
    {
    return false;
  }
}

include("header.php"); 

?>

<style>
label,a, body 
{
	font-family : Arial, Helvetica, sans-serif;
	font-size : 12px; 
}
.err
{
	font-family : Verdana, Helvetica, sans-serif;
	font-size : 12px;
	color: red;
}
</style>	
<!-- a helper script for vaidating the form-->
<script language="JavaScript" src="scripts/gen_validatorv31.js" type="text/javascript"></script>






<script src='https://www.google.com/recaptcha/api.js'></script>

	<div id="banner-area">
		<img src="images/banner/banner2.jpg" alt ="" />
		<div class="parallax-overlay"></div>
			<!-- Subpage title start -->
			<div class="banner-title-content">
	        	<div class="text-center">
		        	<h2>Contact</h2>
		        	
	          	</div>
          	</div><!-- Subpage title end -->
	</div><!-- Banner area end -->

	<!-- Main container start -->

	<section id="main-container">
    
    <div class="row">
				<div class="col-md-12 heading text-center">
					<h2 class="title2">Contact Us
						<span class="title-desc">As a full-service painting contractor, we   love hearing from our clients and community.<br> Whether you have an inquiry about a possible project, a concern with a current or past project, or a question about our company, <br>please give us a call or send us an email. Office Hours: Monday - Friday 8:00 AM - 4:00 PM.</span>
					</h2>
				</div>
			</div><!-- Title row end -->
    
    
		<div class="container">
			
			<div class="row">
				<!-- Map start here -->
				<div id="map-wrapper" class="no-padding">
					<div><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3318.1445739714563!2d150.96728155090594!3d-33.731077480599936!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b12a1eecdc25fb5%3A0x57d6d65b29b5fd75!2s4+Columbia+Ct%2C+Baulkham+Hills+NSW+2153!5e0!3m2!1sen!2sau!4v1492412383647" width="1200" height="350" frameborder="0" style="border:0" allowfullscreen></iframe></div>
				</div><!--/ Map end here -->	

			</div><!-- Content row  end -->

			

			<div class="row">
	    		<div class="col-md-7">
                <form method="POST" name="contact_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
                	    			
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Name</label>
								<input class="form-control" name="name" id="name" value='<?php echo htmlentities($name) ?>' placeholder="" type="text" required>
								</div>
							</div>
                            
                            <div class="col-md-4">
								<div class="form-group">
									<label>Phone number</label>
								<input class="form-control" name="phone" id="phone" placeholder="" type="text" required>
								</div>
							</div>
                            
							<div class="col-md-4">
								<div class="form-group">
									<label>Email</label>
									<input class="form-control" name="email" id="email" value='<?php echo htmlentities($visitor_email) ?>' 
									placeholder="" type="email" required>
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<label for='subject' >How Did You Hear About Us? </label><br>
<select name="subject1"  class="form-control" value='<?php echo htmlentities($visitor_subject1) ?>'>
 <option value="" selected="selected">Select Subject</option>
  <option value="Web Search">Web Search</option>
  <option value="Used King Painting before">Used King Painting before</option>
    <option value="Facebook">Facebook</option>
    <option value="Referred by a Friend">Referred by a Friend</option>
      <option value="Seen Around">Seen Around</option>
         <option value="Community Event">Community Event</option>
         <option value="Other">Other</option>
  


</select> 
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Describe your Project or your question</label>
							<textarea class="form-control" name="message" id="message" placeholder="" rows="10" required><?php echo htmlentities($user_message) ?></textarea>
						</div>
                        
                       <div class="col-md-8">
							
                        
						<div class="text-right"><br>
							<button class="btn btn-primary solid blank" type="submit" value="Submit" name='submit'>Send Message</button> 
						</div>
					</form>
	    		</div>
                
      <script language="JavaScript">
// Code for validating the form
// Visit http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
// for details
var frmvalidator  = new Validator("contact_form");
//remove the following two lines if you like error message box popups
frmvalidator.EnableOnPageErrorDisplaySingleBox();
frmvalidator.EnableMsgsTogether();

frmvalidator.addValidation("name","req","Please provide your name"); 
frmvalidator.addValidation("email","req","Please provide your email"); 
frmvalidator.addValidation("email","email","Please enter a valid email address"); 
</script>
<script language='JavaScript' type='text/javascript'>
function refreshCaptcha()
{
	var img = document.images['captchaimg'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>          
                
                
                
	    		<div class="col-md-5">
	    			<div class="contact-info">
		    			<h3>Contact Details</h3>
			    		
			    		<p><i class="fa fa-home info"></i>  <strong>Office: Level 5, 4 Columbia Court, Bella Vista NSW 2153</strong></p>
						<p><i class="fa fa-phone info"></i><strong>Phone: (02) 8860 6444</strong></p>
						<p><i class="fa fa-phone info"></i><strong>Mobile: 0416 508 346</strong></p>
						<p><i class="fa fa-envelope-o info"></i>  info@kingpainting.com.au</p>
						<p><i class="fa fa-globe info"></i>  www.kingpainting.com.au
    				</div>
	    		</div>
			</div>

		</div><!--/ container end -->

	</section><!--/ Main container end -->
	

	
    
    
    
	<!-- Testimonial start-->
	<section class="testimonial parallax parallax2">
		<div class="parallax-overlay"></div>
	  	<div class="container">
		    <div class="row">
			    <div id="testimonial-carousel" class="owl-carousel owl-theme text-center testimonial-slide">
			        <div class="item">
			          	<div class="testimonial-thumb">
			            	<img src="images/team/testimonial1.jpg" alt="testimonial">
			          	</div>
			          	<div class="testimonial-content">
				            <p class="testimonial-text">
				              I’m very pleased with the painting you did on the outside of my house. Additionally, I was especially pleased with your crew of painters and the fact that you kept in close touch with me. Your crew kept the work site clean and I find no stains anywhere. 
				            </p>
			            	<h3 class="name">Sarah Arevik<span>Chief Executive</span></h3>
			          	</div>
			        </div>
			        <div class="item">
			          	<div class="testimonial-thumb">
			            	<img src="images/team/testimonial2.jpg" alt="testimonial">
			          	</div>
				        <div class="testimonial-content">
				            <p class="testimonial-text">
				              King Painting crew did it again, for the fourth time for us. As before, you did an excellent job. The finished product was exactly what we had in mind, and the work was done quickly and neatly. Excellent work done swiftly by a friendly crew. What more could you ask? Undoubtedly, you’d want an accurate estimate of the price. We got that, too. You can be sure we’ll hire King Painting to do any future painting we need.
				            </p>
				            <h3 class="name">Narek Bedros<span>Sr. Manager</span></h3>
				        </div>
			        </div>
			        <div class="item">
				        <div class="testimonial-thumb">
				            <img src="images/team/testimonial3.jpg" alt="testimonial">
				        </div>
			          	<div class="testimonial-content">
				            <p class="testimonial-text">
				              King Painting painted the exterior of my house. They did an excellent job and were very thorough about making sure everything was to my satisfaction before they finished.
				            </p>
			            	<h3 class="name">Taline Lucine<span>Sales Manager</span></h3>
			          	</div>
			        </div>
			    </div><!--/ Testimonial carousel end-->
		    </div><!--/ Row end-->
	  	</div><!--/  Container end-->
	</section><!--/ Testimonial end-->


	<!-- Clients start -->
	<section id="clients" class="clients wow fadeInDown">
		<div class="container">
        <div class="col-md-12 heading text-center wow fadeIn">
					<h2 class="title2">KING PAINTING ONLY USES THE HIGHEST QUALITY PAINTS
						<span class="title-desc">A Quality  paint is neccessary for all painting company</span>
					</h2>
				</div>
        
			<div class="row wow fadeInLeft">
		      <div id="client-carousel" class="col-sm-12 owl-carousel owl-theme text-center client-carousel">
		        <figure class="item client_logo">
		          <a href="#">
		            <img src="images/clients/client1.jpg" alt="client">
		          </a>
		        </figure>
		        <figure class="item client_logo">
		          <a href="#">
		            <img src="images/clients/client2.jpg" alt="client">
		          </a>
		        </figure>
		        <figure class="item client_logo">
		          <a href="#">
		            <img src="images/clients/client3.jpg" alt="client">
		          </a>
		        </figure>
		        <figure class="item client_logo">
		          <a href="#">
		            <img src="images/clients/client4.jpg" alt="client">
		          </a>
		        </figure>
		        <figure class="item client_logo">
		          <a href="#">
		            <img src="images/clients/client5.jpg" alt="client">
		          </a>
		        </figure>
		        <figure class="item client_logo">
		          <a href="#">
		            <img src="images/clients/client6.jpg" alt="client">
		          </a>
		        </figure>
               
		      </div><!-- Owl carousel end -->
	    	</div><!-- Main row end -->
		</div><!--/ Container end -->
	</section><!--/ Clients end -->
	

	
	<?php include("footer.php"); ?>