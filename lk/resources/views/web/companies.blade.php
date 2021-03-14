(function () {
    "use strict";

    const CompaniesClass = {
        data: [],
        element: null,
        addElement: function (type, innerText = '', attribute = {}, children = null) {
            let el = document.createElement(type);
            if (Object.keys(attribute).length > 0) {
                Object.keys(attribute).forEach(
                    function (each) {
                        el.setAttribute(each, attribute[each]);
                    }.bind({
                        el: el,
                        attribute: attribute,
                    })
                );
            }
            if (children) {
                if (Array.isArray(children)) {
                    children.forEach(function (each) {
                        el.appendChild(each);
                    }.bind({
                        el: el
                    }));
                } else {
                    el.appendChild(children);
                }
            }
            el.innerHTML = innerText + el.innerHTML;
            return el;
        },
        init: function () {
            this.data = JSON.parse("{{ $companies }}");
            let el = document.getElementById("id-companies-rating");
            if (typeof el != "undefined") {
                if (el != null) {
                    this.element = el;
                }
            }
        },
        render: function () {
            let header = this.addElement('H1','Рейтинг компаний', {
                style: 'display: block; text-align: center'
            });
            let main = this.addElement("DIV", 'dsd', {
                style: "max-width: 100%;padding: 20px;background-color:#f5f5f5",
            }, [
                header
            ]);
            this.element.appendChild(main);
        },
    };

    window.addEventListener("load", function (e) {
        CompaniesClass.init();
        CompaniesClass.render();
    });
})();
