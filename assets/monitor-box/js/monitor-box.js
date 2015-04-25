/** @jsx React.DOM */

var MonitorBox = React.createClass({
    loadDataFromServer: function () {
        $.ajax({
            method: 'GET',
            url: this.props.url,
            dataType: 'json',
            success: function (data) {
                this.setState({data: data});
            }.bind(this),
            error: function (xhr, status, err) {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },
    getInitialState: function () {
        return {data: []};
    },
    componentDidMount: function () {
        this.loadDataFromServer();
        setInterval(this.loadDataFromServer, this.props.pollInterval);
    },
    render: function () {
        return (
            <div className="monitor-box">
                <MonitorList data={this.state.data} />
            </div>
        );
    }
});

var MonitorList = React.createClass({
    render: function () {
        if (typeof this.props.data.groups === 'undefined') {
            return false;
        }

        var countColumns = 3;
        var columnIndex = 0;

        var itemsCollapse = this.arrayChunk(this.props.data.groups, Math.ceil(this.props.data.groups.length / countColumns));
        var columnsNodes = itemsCollapse.map(function (column) {
            columnIndex++;
            var colX = Math.floor(12 / countColumns);
            var columnId = 'column' + columnIndex;

            var groupNodes = column.map(function (monitorGroup) {
                return (
                    <MonitorGroup group={monitorGroup} columnId={columnId} />
                );
            });

            return (
                <div className={'col-md-' + colX + ' col-sd-' + colX}>
                    <div id={columnId} className="panel-group collapse in" aria-expanded="true">
                        {groupNodes}
                    </div>
                </div>
            );
        });

        return (
            <div className="columns row">
                {columnsNodes}
            </div>
        );
    },
    arrayChunk: function (input, size) {
        for (var x, i = 0, c = -1, l = input.length, n = []; i < l; i++) {
            (x = i % size) ? n[c][x] = input[i] : n[++c] = [input[i]];
        }

        return n;
    }
});

var MonitorGroup = React.createClass({
    render: function () {
        var counts = {
            error: 0,
            good: 0,
            disable: 0
        };

        var self = this;

        var objectNodes = this.props.group.objects.map(function (object) {
            var parentGroup = self.props.group;

            if (parentGroup.is_disable == 1 || object.is_disable == 1) {
                counts.disable++;
            } else {
                if (object.status <= 0) {
                    counts.error++;
                } else {
                    counts.good++;
                }
            }

            return (
                <MonitorObject object={object} parentGroup={parentGroup} />
            );
        });

        var classSetOptions = {
            'panel': true
        };

        if (this.props.group.is_disable == 1) {
            classSetOptions['panel-warning'] = true;
        } else if (counts.error > 0) {
            classSetOptions['panel-danger'] = true;
        } else if (this.props.group.objects.length > 0) {
            classSetOptions['panel-success'] = true;
        } else {
            classSetOptions['panel-default'] = true;
        }

        var classes = React.addons.classSet(classSetOptions);

        var groupId = this.props.columnId + '-collapse' + this.props.group.id;

        return (
            <div className={classes}>
                <MonitorGroupHeader
                    groupId={groupId}
                    columnId={this.props.columnId}
                    title={this.props.group.name}
                    counts={counts}
                />
                <div id={groupId} className="panel-collapse collapse">
                    <div className="panel-body">
                        {objectNodes}
                    </div>
                </div>
            </div>
        );
    }
});

var MonitorGroupHeader = React.createClass({
    render: function () {

        var d = React.DOM;

        var counterStatuses = [];

        if (this.props.counts.error > 0) {
            if(this.props.counts.good > 0) {
                counterStatuses.push(d.span({className: 'label label-success'}, this.props.counts.good));
            }

            counterStatuses.push(d.span({className: 'label label-danger'}, this.props.counts.error));
        }

        if (this.props.counts.disable > 0) {
            counterStatuses.push(d.span({className: 'label label-warning'}, this.props.counts.disable));
        }

        var counter = d.span({className: 'counter'}, counterStatuses);

        return (
            <div className="panel-heading">
                <h4 className="panel-title">
                    <a className="collapse-toggle" href={'#' + this.props.groupId} data-toggle="collapse" data-parent={'#' + this.props.columnId}>
                        <span className="title">{this.props.title}</span>{counter}
                    </a>
                </h4>
            </div>
        );
    }
});

var MonitorObject = React.createClass({
    render: function () {
        var d = React.DOM;

        var url = '/object/' + this.props.object.id;
        var title = this.props.object.ip + ':' + this.props.object.port;

        var content = [
            d.b({className: 'name'}, this.props.object.name),
            d.span({className: 'ip'}, this.props.object.ip),
            d.i({className: 'last-update'}, moment(this.props.object.updated).startOf('second').fromNow()),
            d.span({className: 'rtt'}, this.props.object.avg_rtt + ' ms')
        ];

        if (typeof this.props.object.lastErrorEventDate !== 'undefined') {
            content.push(
                d.span(
                    {className: 'error-date'},
                    moment(this.props.object.lastErrorEventDate).startOf('second').fromNow()
                )
            );
        }

        var classSetOptions = {
            'obj': true
        };

        if (this.props.parentGroup.is_disable == 1 || this.props.object.is_disable == 1) {
            classSetOptions['disable'] = true;
        } else if (this.props.object.status == 0) {
            classSetOptions['bad'] = true;
        } else {
            classSetOptions['good'] = true;
        }

        var classes = React.addons.classSet(classSetOptions);

        return (
            <a href={url} id={'o_' + this.props.object.id} className={classes} title={title}>
                {content}
            </a>
        );
    }
});

React.render(
    <MonitorBox url="/site/get-monitor-data" pollInterval={6000} />,
    document.getElementById('monitor-box')
);