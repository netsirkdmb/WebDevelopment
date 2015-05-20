Records = new Mongo.Collection("records");

if (Meteor.isClient) {
  Template.body.helpers({
    lastFeeding: function () {
      return "00:00";
    },
    
    records: function () {
      return Records.find({}, {sort: {createdAt: -1}});
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
    
    "click #edit": function (e) {
      Custombox.open({
          target: "#modal-edit",
          effect: "fadein"
      });
      $("#time_edit").attr("value", e.target.attributes.time.value);
      $("#amount_edit").val(e.target.attributes.amount.value);
      $("#record_id").attr("value", e.target.attributes.edit_id.value);
      e.preventDefault();
    },
    
    "click #delete": function (e) {
      Custombox.open({
          target: "#modal-delete",
          effect: "fadein"
      });
      $("#delete_record_id").attr("value", e.target.attributes.delete_id.value);
      e.preventDefault();
    }
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
      
      e.target.time.value = "";
      e.target.amount.value = "";
      
      Custombox.close();
      e.preventDefault();
    },
    
    "click #cancel": function (e) {
      $("#time").val("");
      $("#amount").val("");
      
      Custombox.close();
      e.preventDefault();
    }
  });
  
  Template.edit.events({
    "submit #form-edit": function (e) {
      var time = e.target.time_edit.value;
      var amount = e.target.amount_edit.value;
      var id = e.target.record_id.value;
      Records.update(
        {
          _id:id
        },
        {
          "$set": {
          time: time,
          amount: amount
          }
        }
      );
      
      Custombox.close();
      e.preventDefault();
    }
  });
  
  Template.delete_record.events({
    "click #delete": function (e) {
      var id = $("#delete_record_id").attr("value");
      Records.remove(
        {
          _id:id
        }
      );
      
      Custombox.close();
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
