Records = new Mongo.Collection("records");

if (Meteor.isClient) {
  // counter starts at 0
  Session.setDefault('counter', 0);

  Template.hello.helpers({
    counter: function () {
      return Session.get('counter');
    }
  });
  
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
    "click #add": function ( e ) {
      Custombox.open({
          target: '#modal',
          effect: 'fadein'
      });
      e.preventDefault();
    }
  });

  Template.hello.events({
    'click button': function () {
      // increment the counter when button is clicked
      Session.set('counter', Session.get('counter') + 1);
    }
  });
}

if (Meteor.isServer) {
  Meteor.startup(function () {
    // code to run on server at startup
  });
}
