<html>
	<head>
		<title>Welcome to nginx!</title>
		<link rel="stylesheet" type="text/css" href="css/generic.css">
	</head>
<body bgcolor="white" text="black" style="padding: 0px; margin: 0px;">
<textarea name="input_names" id="input_names" cols="30" rows="10">
</textarea>
<fieldset id="fields_to_populate">
	
</fieldset>


	<div class="canvas apply_cutline portrait" id="canvas0">
		<div class="inner_canvas"></div>
		<div class="cutline t l"></div>
		<div class="cutline b l"></div>
		<div class="cutline t r"></div>
		<div class="cutline b r"></div>
	</div>

	<script type="text/javascript" src="http://provisioning.devshopous.dev/scripts/jquery-1.4.2.js"></script>
	<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->
	<script type="text/javascript">
	var invitations = {};
	//get invitation name
	invitations.chosen_invitation = '<?=$_GET['invitation']?>';
	invitations.control_count = 0;
	invitations.canvas_count = 0;
	
//alert(names[0]);
	invitations.template_element = function (name, innerHTML,CSS_key_pairs){
		this.name = name;
		this.innerHTML = innerHTML;
		this.CSS_key_pairs = CSS_key_pairs;
	}

	invitations.setup_base_template = function(){
		//empty template canvas
		$("#canvas0 .inner_canvas").children().remove();
		//replace base HTML with personalisations
		merged_innerHTML = invitations.template.base_html;
		$.each(invitations.personalised_elements,function(index,obj){
			merged_innerHTML = merged_innerHTML.replace("{" + obj.key + "}",obj.value);
		});	
		//drop base html into template canvas
		$("#canvas0 .inner_canvas").append(merged_innerHTML);
	}

invitations.populate_inner_canvas = function(){
	//remove old canvas'
	for(var i =1; i < invitations.canvas_count; i++){
	$("#canvas" + i).remove();
	}
	invitations.canvas_count = 0;
	//reset template
	
	//load stuff from the template
		
/*(invitations.template.elements,	
											invitations.personalised_elements,
											invitations.sheet_personalisations)	*/

	//add all new canvas' based on template- ID ascends from zero.
	for(var i=0; i < invitations.sheet_personalisations.length;i++){
	invitations.canvas_count +=1;
	var clone = $("#canvas0").clone();
	clone.attr('id',"canvas" + invitations.canvas_count);
	clone.addClass("clone");
	clone.appendTo("body");
	}

	//somehow merge in the personalisation..
	personalised_invitations = 0;
	$.each(invitations.sheet_personalisations,function(i,person){
		personalised_invitations +=1;
			var this_sheet = $("#canvas" + personalised_invitations + " .inner_canvas");
			this_sheet.children().each(function(){
				var contents = $(this).html();
				if(contents.indexOf("{" + person.key + "}") > -1){
					$(this).html(contents.replace("{" + person.key + "}",person.value));
				}
				console.log($(this).attr('id'));
			});
	})


}
invitations.template = {};
invitations.template.elements = [];

invitations.template.fields_required = [];

	invitations.personalised_elements= [];
	invitations.personalised_elements.push(	);
	
	invitations.sheet_personalisations = [];


$(function(){

	//user has changed a recipient
	$("#input_names").change(function(){
		invitations.sheet_personalisations = []; //clear and repopulate array.
		var contents = $("#input_names").val();
		var names = contents.split("\n");
		$.each(names,function(index,name){
			invitations.sheet_personalisations.push({"key":"person_name","value":name});
		});
		invitations.populate_inner_canvas();
	});

	//user has changed a personalised element.
	$(".field_to_populate").live('change',function(){
		var field_name = $(this).attr('name');
		var field_value = $(this).val();
		$.each(invitations.personalised_elements,function(index,personalised_element){
			if(personalised_element.key == field_name){
				personalised_element.value = field_value;
			}
		})
			invitations.setup_base_template();
			invitations.populate_inner_canvas();
	})

	$.ajaxSetup({
	  error: function(xhr, status, error) {
	    alert("An AJAX error occured: " + status + "\nError: " + error);
	  }
		});

		//send off for the invitation base template
		$.get(invitations.chosen_invitation + ".html",{},function(data){
			invitations.template.base_html = data;
			
		//send off for the fields user populates ONCE.
		$.get(invitations.chosen_invitation + "_kv.json",{},function(data2){
			//var dataobj = $.parseJSON(data2);
			$("#fields_to_populate").html("");
			$.each(data2.fields,function(index,variable_data_field){
				invitations.personalised_elements.push(variable_data_field);
				if(variable_data_field.friendly_name != undefined){
					$("#fields_to_populate").append(variable_data_field.friendly_name);
				}
				if(variable_data_field.fieldtype == undefined || variable_data_field.fieldtype=="text"){
					//if not specified or text, output a text field.
					$("#fields_to_populate").append("<input name='" + variable_data_field.key + "' type='text' value='" + variable_data_field.value + "' class='field_to_populate' /><br />");
				}else{
					//if specifically a textarea, output.
					$("#fields_to_populate").append("<textarea name='" + variable_data_field.key + "' type='text' class='field_to_populate'>" + variable_data_field.value + "</textarea><br />");
				}
			});
			invitations.setup_base_template();
			invitations.populate_inner_canvas();	
		});
})

//merge in dummy data
/*	populate_inner_canvas(template.elements,
											personalised_elements,
											sheet_personalisations);*/

});
</script>

</body>
</html>