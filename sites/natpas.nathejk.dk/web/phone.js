ko.bindingHandlers.console = {
    init: function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        console.log('init', valueAccessor());
    },
    update: function(element, valueAccessor) {
//        console.log('update', valueAccessor);
    }
}
function Grouping(groupName, title, teams, members) {
    var self = this;
    this.groupName = ko.observable(groupName);
    this.title = ko.observable(title);
    this.teams = teams;
    this.members = ko.observableArray(members);

    /*
    this.teams = ko.observableArray(
        ko.utils.arrayMap(teams, function(team) {
            return new Team(team.id, team.title, team.members);
        })
    );
    this.teamNames = ko.computed(function() {
        var teamNames = [];
        ko.utils.arrayMap(self.teams(), function(team) {});
    });
    this.membersdd = ko.computed(function() {
        var members = [];
        ko.utils.arrayMap(self.teams(), function(team) {
            ko.utils.arrayMap(team.members(), function(member) {
                member.teamId = team.id();
                member.teamName = team.name();
                members.push(member);
            });
        });
        return members;
    });
    */
    this.addMember = function() { console.log('adding for vildt');}
}
function Team(id, name, type, members) {
    var self = this;
    this.id = ko.observable(id);
    this.name = ko.observable(name);
    this.type = ko.observable(type);
    this.members = ko.observableArray(members);
    /*ko.utils.arrayMap(members, function(member) {
        return new Member(member.id, member.title, member.phone, self.id());
    }));*/
}
function Member(id, name, phone, teamId) {
    var self = this;
    this.id = ko.observable(id);
    this.name = ko.observable(name);
    this.phone = ko.observable(phone);
    this.teamId = ko.observable(teamId);
}
var Model = function() {
    var self = this;
    self.members = ko.observableArray();
    self.allData = ko.observable();
    /*self.groupings = ko.computed(function() {
        var groups = {};
        ko.utils.arrayMap(self.members, function(member) {
            if (typeof groups[member.grouping] == 'undefined') groups[member.grouping] = [];
            groups[member.grouping].push(member);
        });
        for (groupName in groups) {
            groups[groupName] = new Grouping(groupName, groups[groupName]);
        }
        return groups;
    });
    self.addMember = function() {
        self.members.push({
            title: "",
            phone: "",
            grouping: ''
        });
    };*/
    self.groups = ko.computed(function() {
        var groups = [];
        ko.utils.arrayMap(self.allData(), function(group) {
            var teams = ko.utils.arrayFilter(self.teams(), function(team) { return team.type() == group.typeName});
            var teamIds = ko.utils.arrayMap(teams, function(team) { return team.id(); });
            var members = ko.utils.arrayFilter(self.members(), function(member) { return teamIds.indexOf(member.teamId()) >= 0});
            if (teams.length) groups.push(new Grouping(group.typeName, group.title, teams, members));
        });
        var members = ko.utils.arrayFilter(self.members(), function(member) { return !parseInt(member.teamId()); });
        if (members.length) groups.push(new Grouping('new', 'Nye brugere', [{name:'Vælg placering', id:0}], members));
        return groups;
    });
    self.teamNames = [];
    self.teams = ko.computed(function() {
        var teams = [], members = [];
        ko.utils.arrayMap(self.allData(), function(group) {
            ko.utils.arrayMap(group.teams, function(team) {
                var members = ko.utils.arrayFilter(self.members, function(member) { return member.teamId() == team.id()});
                teams.push(new Team(team.id, team.title, group.typeName, members));
            });
        });
        return teams;
    });
    $.getJSON("?json", function(groups) {
        self.allData(groups);
        var members = [];
        ko.utils.arrayMap(groups, function(group) {
            ko.utils.arrayMap(group.teams, function(team) {
                //teams[group.typeName].push(team.name);
                ko.utils.arrayMap(team.members, function(member) {
                    members.push(new Member(member.id, member.title, member.phone, team.id));
                });
            });
        });
        //self.teamNames = teams;
        self.members(members);
    }); 

    self.addMember = function() {
        self.members.push(new Member(null,null,null,0));

        //console.log(ko.utils.arrayMap(self.members(), function (m) {return m.teamId();}));
    };
    self.removeMember = function(gift) {
        self.members.remove(gift);
    };

    self.save = function(form) {
        //alert("Could now transmit to server: " + ko.utils.stringifyJson(self.members));
        // To actually transmit to server as a regular form post, write this: 
        ko.utils.postJson($("form")[0], ko.toJS(self.members()));
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
