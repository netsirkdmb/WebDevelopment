Records = new Mongo.Collection("records");

if (Meteor.isClient) {
  Template.body.helpers({
    lastFeeding: function () {
      return "00:00";
    },
    
    records: function () {
      return Records.find({});
    }
  });
  
  // use custombox
  // http://dixso.github.io/custombox/
  Template.body.events({
    "click #add": function (e) {
      Custombox.open({
          target: "#modal-add",
          effect: "fadein"
      });
      e.preventDefault();
    },
    
    "click #settings": function (e) {
      Custombox.open({
          target: "#modal-settings",
          effect: "fadein"
      });
      e.preventDefault();
    }, 
    
  });
  
  Template.add.events({
    "submit #form-add": function (e) {
      var time = e.target.time.value;
      var amount = e.target.amount.value;
      
      Records.insert({
        time: time,
        amount: amount,
        createdAt: new Date() // current time
      });
      
      event.target.time.value = "";
      event.target.amount.value = "";
      
      e.preventDefault();
    }
  });
  
  Template.record.events({
    "click #edit": function (e) {
      // Custombox.open({
          // target: "#modal-add",
          // effect: "fadein"
      // });
      // e.preventDefault();
      console.log(e.target.attributes.edit_id);
    }
  });
}

if (Meteor.isServer) {
  Meteor.startup(function () {
    // code to run on server at startup
  });
}
