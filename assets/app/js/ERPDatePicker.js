var ERPDatePicker = function (selector, options)
{
    var self = this;
    self.selector = selector;
    self.options = $.extend(true,
    {
        format: "dd/MM/yyyy",
        parseFormats:
        [
            "dd/MM/yyyy",
            "d/M/yyyy",
            "yyyy-MM-dd"
        ],

        value: new Date(),
        animation:
        {
            open:
            {
                effects:"fadeIn"
            }
        },
        footer:false,
        dateInput:true,
        culture:"es-MX"

    },options);

    self.init = function()
    {
        $(self.selector).kendoDatePicker(self.options);
        self.widget=$(self.selector).data("kendoDatePicker");
        self.widget.wrapper.addClass("erp-datepicker");

        return self.widget;
    };
    self.value=function(value)
    {
        if(value===undefined)
            return self.widget.value();
        self.widget.value(value);
    };
    self.enable=function(enable)
    {
        self.widget.enable(enable);
    };
    self.readonly=function(state)
    {
        self.widget.readonly(state);
    };
};