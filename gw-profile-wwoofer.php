<!-- This just outputs every visible profile field that has any contents -->
<div class="row">
  <div class="col-sm-12 col-md-12">
    <div class="panel panel-success">
<div class="panel-heading">
        <h3 class="panel-title"><span class="icon-picture"> </span> Gallery</h3>
      </div>
      <div class="panel-body">
        <div class="mpp-g mpp-item-list mpp-media-list mpp-photo-list mpp-single-gallery-media-list mpp-single-gallery-photo-list">   
            {% for item in imgs|slice(0,8) %}
            <div class="mpp-u mpp-item mpp-media mpp-media-photo mpp-u-6-24">
              <div class="mpp-item-entry mpp-media-entry mpp-photo-entry">       
                <div class="mpp-container mpp-media-list mpp-activity-comment-photo-list" style="padding:5px;">		
              <a href="{{item.src}}" data-mpp-context="photo">
                <img src="{{item.thumb}}" class="mpp-attached-media-item"  title="" data-mpp-media-id="{{item.id}}">
              </a>
              </div>
            </div>
          </div>
            {% endfor %}
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row">
<!-- Visible on mobile -->
  <div class="col-sm-12 col-md-8 visible-sm visible-xs">  
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="icon-home"> </span> About Me</h3>
      </div>
      <div class="panel-body">
        {% for item in data.AboutMe %}

	 <h4 class="{{item.icon}}">{{item.name|striptags}}</h4> 
	 <p>{{item.val}}</p>
      
        
        {% endfor %}
      </div>
    </div>
  </div> 
  <!-- END VISIBLE MOBILE -->
  
  <div class="col-sm-12 col-md-4">  
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="icon-user-1"> </span> My Profile</h3>
      </div>
      <div class="panel-body text-center">
        {% for item in data.MyProfile %}
        <h4 class="prosubs">{{item.name}}</h4>
        {{item.val}}
        {% endfor %}
      </div>
    </div>
    



<!-- This just outputs every visible profile field that has any contents -->

  
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="icon-two114"> </span> Attributes</h3>
      </div>
      <div class="panel-body">
        {% for item in data.PersonalAttributes %}
        <h4 class="prosubs">{{item.name}}</h4>
        {{item.val}}
        {% endfor %}
      </div>
    </div>
   

  
   
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="icon-home"> </span> Address</h3>
      </div>
      <div class="panel-body">
        {% for item in data.Address %}
        <h4 class="prosubs">{{item.name}}</h4>
        {{item.val}}
        {% endfor %}
      </div>
    </div>
  
 
   
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="icon-phone"> </span> Contact</h3>
      </div>
      <div class="panel-body">
        {% for item in data.Contact %}
        <h4 class="prosubs">{{item.name}}</h4>
        {{item.val}}
        {% endfor %}
      </div>
    </div>  
  </div>  



<!-- HIDDEN ON MOBILE -->
  <div class="col-sm-12 col-md-8 hidden-sm hidden-xs">   
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="icon-user-1"> </span> About Me</h3>
      </div>
      <div class="panel-body text-center">
        {% for item in data.AboutMe %}
        <h4 class="prosubs">{{item.name}}</h4>
        {{item.val}}
        {% endfor %}
      </div>
    </div>
  
<!-- Calender shortcode -->
<div class="row">
<div class="col-sm-12 col-md-12">
<div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="icon-calendar"> </span> Availability Calendar</h3>
      </div>
      <div class="panel-body">
[gw-calendar]
	</div>
	</div>
</div>
</div>
<!-- END CALENDAR -->
  </div>
</div>



<!-- This loops each review and outputs the title and stars rating-->


<div class="row">
<div class="col-sm-12">
[gmw_single_location element_id="9999" item_type="member" item_id="{{id}}" elements="map,address" map_width="100%"]
</div>
{{ userreview.screen_content() }}

<h2>External Reviews</h2>
{% for item in reviews %}
<div class="col-sm-12 col-md-6">

<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">{{item.stars}} stars &nbsp;&nbsp;
{% for i in range(1, item.stars) %}
<span style="color:#179b05" class="icon-star"></span>
{% endfor %}
&nbsp;&nbsp;Reviewed by:&nbsp;&nbsp; <a href="{{item.backlink}}"><span style="color:#179b05">{{item.author_name}}</span></a>
</h3>
  </div>
  <div class="panel-body">
{{item.review}}
<p></p>
</div>
</div>
</div>

{% endfor %}
</div>