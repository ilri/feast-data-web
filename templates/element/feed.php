<div id="feed-container">
    <div class='col-md-12' data-bind='foreach: feed'>
        <div class='feed-item'>
            <div class='feed-title'>
                <h2><a target='_blank' data-bind="attr: {href:url}, text: title"></a></h2>
            </div>
            <div class="feed-date">
                <p data-bind="text: moment(new Date(xml.pubDate)).format('L')"></p>
            </div>
            <div class="feed-content">
                <p data-bind="html: preview"></p>
            </div>
        </div>
    </div>
</div>