 
$.fn.wizard = function(config) {
  if (!config) {
    config = {};
  };
  var containerSelector = config.containerSelector || ".wizard-content";
  var stepSelector = config.stepSelector || ".wizard-step";
  var steps = $(this).find(containerSelector+" "+stepSelector);
  var stepCount = steps.size();
  var exitText = config.exit || 'Cancel';
  var backText = config.back || 'Back';
  var nextText = config.next || 'Next';
  var finishText = config.finish || 'Finish';
  var isModal = config.isModal || true;
  var validateNext = config.validateNext || function(){return true;};
  var validateFinish = config.validateFinish || function(){return true;};
    //////////////////////
    var step = 1;
    var container = $(this).find(containerSelector);
    steps.hide();
    $(steps[0]).show();
    if (isModal) {
      $(this).on('hidden.bs.modal', function () {
        step = 1;
        $($(containerSelector+" .wizard-steps-panel .step-number")
          .removeClass("done")
          .removeClass("doing")[0])
        .toggleClass("doing");
        
        $($(containerSelector+" .wizard-step")
          .hide()[0])
        .show();

        btnBack.hide();
        btnExit.show();
        btnFinish.hide();
        btnNext.show();

      });
    };
    $(this).find(".wizard-steps-panel").remove();
    container.prepend('<div class="wizard-steps-panel steps-quantity-'+ stepCount +'"></div>');
    var stepsPanel = $(this).find(".wizard-steps-panel");
    for(s=1;s<=stepCount;s++){
      stepsPanel.append('<div class="step-number step-'+ s +'"><div class="number">'+ s +'</div></div>');
    }
    $(this).find(".wizard-steps-panel .step-"+step).toggleClass("doing");
    //////////////////////
    var contentForModal = "";
    if(isModal){
      contentForModal = ' data-dismiss="modal"';
    }
    var btns = "";
    btns += '<button type="button" class="btn btn-default wizard-button-exit"'+contentForModal+'>'+ exitText +'</button>';
    btns += '<button type="button" class="btn btn-default wizard-button-back">'+ backText +'</button>';
    btns += '<button type="button" class="btn btn-default wizard-button-next">'+ nextText +'</button>';
    btns += '<input type="submit" class="btn btn-primary wizard-button-finish" id="add_button" value="Submit" />';
    $(this).find(".wizard-buttons").html("");
    $(this).find(".wizard-buttons").append(btns);
    var btnExit = $(this).find(".wizard-button-exit");
    var btnBack = $(this).find(".wizard-button-back");
    var btnFinish = $(this).find(".wizard-button-finish");
    var btnNext = $(this).find(".wizard-button-next");

    btnNext.on("click", function () {
      if(!validateNext(step, steps[step-1])){
        return;
      };
      $(container).find(".wizard-steps-panel .step-"+step).toggleClass("doing").toggleClass("done");
      step++;
      steps.hide();
      $(steps[step-1]).show();
      $(container).find(".wizard-steps-panel .step-"+step).toggleClass("doing");
      if(step==stepCount){
        btnFinish.show();
        btnNext.hide();
      }
      //added this for validation for forms
      if(!!config.onnext){
        config.onnext();
      }
      
      btnExit.hide();
      btnBack.show();
    });

    btnBack.on("click", function () {
      $(container).find(".wizard-steps-panel .step-"+step).toggleClass("doing");
      step--;
      steps.hide();
      $(steps[step-1]).show();
      $(container).find(".wizard-steps-panel .step-"+step).toggleClass("doing").toggleClass("done");
      if(step==1){
        btnBack.hide();
        btnExit.show();
      }
      btnFinish.hide();
      btnNext.show();
    });

    btnFinish.on("click", function () {
      btnFinish.val("Saving, Please Wait...");
      btnFinish.attr("disabled", true);
      if(!validateFinish(step, steps[step-1])){
        return;
      };
      if(!!config.onfinish){
        config.onfinish();
      }
    })

    btnBack.hide();
    btnFinish.hide();
    return this;
  };
