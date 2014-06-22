ko.bindingHandlers.console = {
    init: function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        console.log('init', valueAccessor());
    },
    update: function(element, valueAccessor) {
        console.log('update', valueAccessor());
    }
}
function Grouping(groupName, title, teams) {
    var self = this;
    this.groupName = ko.observable(groupName);
    this.title = ko.observable(title);
    this.teams = teams;
}
function Team(id, name, type) {
    var self = this;
    this.id = ko.observable(id);
    this.name = ko.observable(name);
    this.type = ko.observable(type);
}
var Model = function() {
    var self = this;
    self.teams = ko.observableArray();
    self.allData = ko.observable();
    
    self.groups = ko.computed(function() {
        var groups = [];
        ko.utils.arrayMap(self.allData(), function(group) {
            var teams = ko.utils.arrayFilter(self.teams(), function(team) { return team.type() == group.typeName});
            //var teamIds = ko.utils.arrayMap(teams, function(team) { return team.id(); });
            //var members = ko.utils.arrayFilter(self.members(), function(member) { return teamIds.indexOf(member.teamId()) >= 0});
            groups.push(new Grouping(group.typeName, group.title, teams));
        });
        //console.log(groups);
        return groups;
    });
    $.getJSON("phone.php?json", function(groups) {
        self.allData(groups);
        var teams = [];
        ko.utils.arrayMap(groups, function(group) {
            ko.utils.arrayMap(group.teams, function(team) {
            //wconsole.log(group);
                teams.push(new Team(team.id, team.title, group.typeName));
            });
        });
        //self.teamNames = teams;
        self.teams(teams);
    }); 

    self.addTeam = function() {
        self.teams.push(new Team());

        //console.log(ko.utils.arrayMap(self.members(), function (m) {return m.teamId();}));
    };
    self.removeMember = function(gift) {
        self.members.remove(gift);
    };

    self.save = function(form) {
        //alert("Could now transmit to server: " + ko.utils.stringifyJson(self.members));
        // To actually transmit to server as a regular form post, write this: 
        ko.utils.postJson($("form")[0], ko.toJS(self.teams()));
    };
};

var viewModel = new Model();
/*[
    { title: "Tall Hat", phone: "39.95", grouping:'backseat driver'},
    { title: "Long Cloak", phone: "120.00", grouping:'backseat driver'}
]);*/
ko.applyBindings(viewModel);

// Activate jQuery Validation
//$("form").validate({ submitHandler: viewModel.save });
