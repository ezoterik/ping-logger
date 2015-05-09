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
    handleChangeWideMode: function (newIsWideMode) {
        this.setProps({isWideMode: newIsWideMode});
    },
    render: function () {
        var list = <MonitorListByGroup data={this.state.data} />;

        if (this.props.isWideMode) {
            list = <MonitorList data={this.state.data} />;
        }

        return (
            <div className="monitor-box">
                {list}
                <MonitorModeToggleButton onChangeWideMode={this.handleChangeWideMode} isChecked={this.props.isWideMode} />
            </div>
        );
    }
});

var MonitorModeToggleButton = React.createClass({
    getInitialState: function () {
        var checkedValue = false;

        if (typeof this.props.isChecked !== 'undefined') {
            checkedValue = this.props.isChecked;
        }

        return {checked: checkedValue};
    },
    handleChange: function (e) {
        //console.log(e.target.checked);
        var newCheckedState = !this.state.checked;
        this.setState({checked: newCheckedState});
        this.props.onChangeWideMode(newCheckedState);
    },
    render: function () {
        return (
            <div className="checkbox">
                <label><input onChange={this.handleChange} type="checkbox" checked={this.state.checked} /> Широкий экран</label>
            </div>
        );
    }
});

var MonitorListByGroup = React.createClass({
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
                    <MonitorGroup key={monitorGroup.id} group={monitorGroup} columnId={columnId} />
                );
            });

            return (
                <div key={columnIndex} className={'col-md-' + colX + ' col-sd-' + colX}>
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

        var objectNodes = [];

        if (typeof this.props.group.objects !== 'undefined') {
            objectNodes = this.props.group.objects.map(function (object) {
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
                    <MonitorObject key={object.id} object={object} parentGroup={parentGroup} />
                );
            });
        }

        var classSetOptions = {
            'panel': true
        };

        if (this.props.group.is_disable == 1) {
            classSetOptions['panel-warning'] = true;
        } else if (counts.error > 0) {
            classSetOptions['panel-danger'] = true;
        } else if (typeof this.props.group.objects !== 'undefined' && this.props.group.objects.length > 0) {
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
                    lockDate={this.props.group.lock_date}
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

var MonitorList = React.createClass({
    render: function () {
        if (typeof this.props.data.groups === 'undefined') {
            return false;
        }

        var objects = [];
        //Вынимаем все объекты в плоский массив
        this.props.data.groups.forEach(function (group) {
            if (typeof group.objects === 'undefined') {
                return false;
            }

            group.objects.forEach(function (object) {
                object.parentGroup = group;
                objects.push(object);
            }.bind(this));
        }.bind(this));

        //Сортируем
        objects = objects.sort(function (a, b) {
            //Сортировка по отключенности
            var aDisable = (a.parentGroup.is_disable == 1 || a.is_disable == 1);
            var bDisable = (b.parentGroup.is_disable == 1 || b.is_disable == 1);

            if (aDisable > bDisable) {
                return 1;
            } else if (aDisable < bDisable) {
                return -1;
            }

            //Сортировка по статусу
            if (parseInt(a.status) > parseInt(b.status)) {
                return 1;
            } else if (parseInt(a.status) < parseInt(b.status)) {
                return -1;
            }

            //Сортировка по ID
            if (parseInt(a.id) > parseInt(b.id)) {
                return -1;
            } else if (parseInt(a.id) < parseInt(b.id)) {
                return 1;
            }

            return 0;
        });

        var objectsNodes = objects.map(function (object) {
            return (
                <MonitorObject key={object.id} object={object} parentGroup={object.parentGroup} />
            );
        });

        return (
            <div className="list">
                {objectsNodes}
            </div>
        );
    }
});

var MonitorGroupHeader = React.createClass({
    render: function () {
        var d = React.DOM;

        var counterStatuses = [];

        if (this.props.counts.error > 0) {
            if (this.props.counts.good > 0) {
                counterStatuses.push(d.span({key: 1, className: 'label label-success'}, this.props.counts.good));
            }

            counterStatuses.push(d.span({key: 2, className: 'label label-danger'}, this.props.counts.error));
        }

        if (this.props.counts.disable > 0) {
            counterStatuses.push(d.span({key: 3, className: 'label label-warning'}, this.props.counts.disable));
        }

        var counter = d.span({key: 'counter', className: 'counter'}, counterStatuses);

        var pingStatus = [];
        if (this.props.lockDate !== '0000-00-00 00:00:00') {
            pingStatus = d.span(
                {
                    key: 'status',
                    className: 'ping-status pull-right',
                    'title': 'Start ping: ' + moment(this.props.lockDate).startOf('second').fromNow(),
                    'data-toggle': 'tooltip'
                },
                d.i({key: 'refresh-icon', className: 'fa fa-refresh fa-spin'}, '')
            );
        }

        return (
            <div className="panel-heading">
                <h4 className="panel-title">
                    <a className="collapse-toggle" href={'#' + this.props.groupId} data-toggle="collapse" data-parent={'#' + this.props.columnId}>
                        <span className="title">{this.props.title}</span>{counter}
                    </a>
                    {pingStatus}
                </h4>
            </div>
        );
    }
});

var MonitorObject = React.createClass({
    render: function () {
        var d = React.DOM;

        var url = '/object/' + this.props.object.id;
        var title = this.props.object.ip;
        if (this.props.object.port > 0) {
            title += ':' + this.props.object.port;
        }

        var content = [
            d.b({key: 'name', className: 'name'}, this.props.object.name),
            d.span({key: 'ip', className: 'ip'}, this.props.object.ip),
            d.i({key: 'last-update', className: 'last-update'}, moment(this.props.object.updated).startOf('second').fromNow()),
            d.span({key: 'rtt', className: 'rtt'}, this.props.object.avg_rtt + ' ms')
        ];

        if (typeof this.props.object.lastErrorEventDate !== 'undefined') {
            content.push(
                d.span(
                    {key: 'error-date', className: 'error-date'},
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
            <a href={url} id={'o_' + this.props.object.id} className={classes} title={title} data-toggle="tooltip">
                {content}
            </a>
        );
    }
});

$(function () {
    React.render(
        <MonitorBox isWideMode={false} url="/site/get-monitor-data" pollInterval={5000} />,
        document.getElementById('monitor-box')
    );
});