<!-- BEGIN: MAIN -->
<div class="admin-panel-title">
    <h1>{PHP.L.visitor_stats}</h1>
</div>

<div class="block">
    <div class="button-toolbar">
        <form method="get" action="{PHP|cot_url('admin', 'm=other&p=visitor_stats')}" class="inline">
            <label>{PHP.L.visitor_stats_period}:</label>
            <input type="number" name="days" value="{VISITOR_STATS_DAYS}" min="1" max="365" />
            <label>
                <input type="checkbox" name="exclude_bots" value="1" {IF VISITOR_STATS_EXCLUDE_BOTS}checked="checked"{/IF} />
                {PHP.L.visitor_stats_exclude_bots}
            </label>
            <button type="submit" class="button">{PHP.L.Apply}</button>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-4 gap-3">
    <div class="block">
        <h3>{PHP.L.visitor_stats_total_visits}</h3>
        <div class="stat-value">{VISITOR_STATS_TOTAL_VISITS}</div>
    </div>
    <div class="block">
        <h3>{PHP.L.visitor_stats_human_visits}</h3>
        <div class="stat-value" style="color: #28a745;">{VISITOR_STATS_HUMAN_VISITS}</div>
    </div>
    <div class="block">
        <h3>{PHP.L.visitor_stats_bot_visits}</h3>
        <div class="stat-value" style="color: #ffc107;">{VISITOR_STATS_BOT_VISITS}</div>
    </div>
    <div class="block">
        <h3>{PHP.L.visitor_stats_unique_visitors}</h3>
        <div class="stat-value" style="color: #007bff;">{VISITOR_STATS_UNIQUE_VISITORS}</div>
    </div>
</div>

<!-- Daily Breakdown -->
<!-- BEGIN: DAILY_BREAKDOWN -->
<div class="block">
    <h2>{PHP.L.visitor_stats_daily_breakdown}</h2>
    <div class="wrapper">
        <table class="cells">
            <thead>
                <tr>
                    <th class="w-15">{PHP.L.visitor_stats_date}</th>
                    <th class="w-15 textcenter">{PHP.L.visitor_stats_visits}</th>
                    <th class="w-10 textcenter">{PHP.L.visitor_stats_humans}</th>
                    <th class="w-10 textcenter">{PHP.L.visitor_stats_bots}</th>
                    <th class="w-50 textcenter">{PHP.L.Graph}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: ROW -->
                <tr class="{VISITOR_STATS_DAY_ODDEVEN}">
                    <td>{VISITOR_STATS_DAY_DATE}</td>
                    <td class="textcenter">{VISITOR_STATS_DAY_COUNT}</td>
                    <td class="textcenter" style="color: #28a745;">{VISITOR_STATS_DAY_HUMANS}</td>
                    <td class="textcenter" style="color: #ffc107;">{VISITOR_STATS_DAY_BOTS}</td>
                    <td class="textcenter">
                        <div class="bar_back">
                            <div class="bar_front" style="width:{VISITOR_STATS_DAY_PERCENTBAR}%;"></div>
                        </div>
                    </td>
                </tr>
                <!-- END: ROW -->
            </tbody>
        </table>
    </div>
</div>
<!-- END: DAILY_BREAKDOWN -->

<!-- Top Pages -->
<!-- BEGIN: TOP_PAGES -->
<div class="block">
    <h2>{PHP.L.visitor_stats_top_pages}</h2>
    <div class="wrapper">
        <table class="cells">
            <thead>
                <tr>
                    <th class="w-70">{PHP.L.visitor_stats_page}</th>
                    <th class="w-30 textcenter">{PHP.L.visitor_stats_visits}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: ROW -->
                <tr class="{VISITOR_STATS_PAGE_ODDEVEN}">
                    <td><code>{VISITOR_STATS_PAGE_URL}</code></td>
                    <td class="textcenter">{VISITOR_STATS_PAGE_COUNT}</td>
                </tr>
                <!-- END: ROW -->
            </tbody>
        </table>
    </div>
</div>
<!-- END: TOP_PAGES -->

<!-- Top Referrers -->
<!-- BEGIN: TOP_REFERERS -->
<div class="block">
    <h2>{PHP.L.visitor_stats_top_referers}</h2>
    <div class="wrapper">
        <table class="cells">
            <thead>
                <tr>
                    <th class="w-70">{PHP.L.visitor_stats_referer}</th>
                    <th class="w-30 textcenter">{PHP.L.visitor_stats_visits}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: ROW -->
                <tr class="{VISITOR_STATS_REFERER_ODDEVEN}">
                    <td>{VISITOR_STATS_REFERER}</td>
                    <td class="textcenter">{VISITOR_STATS_REFERER_COUNT}</td>
                </tr>
                <!-- END: ROW -->
            </tbody>
        </table>
    </div>
</div>
<!-- END: TOP_REFERERS -->

<!-- Top Crawlers -->
<!-- BEGIN: TOP_CRAWLERS -->
<div class="block">
    <h2>{PHP.L.visitor_stats_top_crawlers}</h2>
    <div class="wrapper">
        <table class="cells">
            <thead>
                <tr>
                    <th class="w-70">{PHP.L.visitor_stats_crawler}</th>
                    <th class="w-30 textcenter">{PHP.L.visitor_stats_visits}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: ROW -->
                <tr class="{VISITOR_STATS_CRAWLER_ODDEVEN}">
                    <td>{VISITOR_STATS_CRAWLER_NAME}</td>
                    <td class="textcenter">{VISITOR_STATS_CRAWLER_COUNT}</td>
                </tr>
                <!-- END: ROW -->
            </tbody>
        </table>
    </div>
</div>
<!-- END: TOP_CRAWLERS -->

<!-- END: MAIN -->