export default () => {
  if (process.server) {
    return false;
  }
  let sider = document.querySelector(".setting-layout-sider");
  var touchStart;
  if (sider) {
    sider.addEventListener("touchstart", e => {
      let sumTouch = 0;
      e.changedTouches.forEach(each => {
        sumTouch += each.clientX;
      });
      touchStart = Math.ceil(sumTouch / e.changedTouches.length);
    });
    sider.addEventListener("touchend", e => {
      let sumTouch = 0;
      let touchEnd = 0;

      e.changedTouches.forEach(each => {
        sumTouch += each.clientX;
      });
      touchEnd = Math.ceil(sumTouch / e.changedTouches.length);
      if (touchEnd > touchStart) {
        sider.classList.add("open");
      } else {
        sider.classList.remove("open");
      }
    });
  }
};
