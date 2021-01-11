var feedModel = null;
function requestFailed(jqXHR, textStatus, errorThrown, errorObservable) {
    if (jqXHR.status === 401) {
        window.location.replace("/");
    } else if (jqXHR.status === 403) {
        window.location.replace("/timeout");
    } else {
        // TODO: Clean these up and make them readable.
        errorObservable(stringSystemError);
    }
}

/**
 * This model will handle RSS feeds
 */
function koFeedModel() {
    var self = this;

    self.lastError = ko.observable(null);
    self.initialized = ko.observable(false);
    self.reportError = function() {
        $('#errorModal').modal('show');
    };

    self.feed = ko.observableArray();

    /*** MODEL HOUSEKEEPING **/
    self.initialize = function() {
        self.initialized(true);
        getData('/api/feed', function(data) {
            for (var i = 0; i < data.feed.items.length; i++) {
                var thisItem = data.feed.items[i];
                thisItem.preview = getLeadingHtml(thisItem.content,500);
            }
            self.feed(data.feed.items);
        });
    };
}

function getData(url, callback) {
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, feedModel.lastError);
    });
}

$(document).ready(function() {
    feedModel = new koFeedModel();
    feedModel.initialize();
    ko.applyBindings(feedModel, document.getElementById('feed-container'));
});

function getLeadingHtml(input, maxChars) {
    // token matches a word, tag, or special character
    var token = /\w+|[^\w<]|<(\/)?(\w+)[^>]*(\/)?>|</g,
            selfClosingTag = /^(?:[hb]r|img)$/i,
            output = "",
            charCount = 0,
            openTags = [],
            match;

    // Set the default for the max number of characters
    // (only counts characters outside of HTML tags)
    maxChars = maxChars || 500;

    while ((charCount < maxChars) && (match = token.exec(input))) {
        // If this is an HTML tag
        if (match[2]) {
            output += match[0];
            // If this is not a self-closing tag
            if (!(match[3] || selfClosingTag.test(match[2]))) {
                // If this is a closing tag
                if (match[1])
                    openTags.pop();
                else
                    openTags.push(match[2]);
            }
        } else {
            charCount += match[0].length;
            if (charCount <= maxChars)
                output += match[0];
        }
    }

    // Close any tags which were left open
    output += '...';
    var i = openTags.length;
    while (i--)
        output += "</" + openTags[i] + ">";
    
    return output;
}
