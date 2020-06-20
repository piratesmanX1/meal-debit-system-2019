const arrows = document.querySelector('.quantity').querySelectorAll('.fa');

const handleChange = (elem)=>{
  const qt = document.querySelector('#qt');
  const total = document.querySelector('#price');
  let value = parseInt(qt.innerText);
  let classArr = Array.from(elem.classList);
  if(/right/gi.test(classArr)) {
    if(value!=9) value++;
    else alert('Watch out! We\'ve got a badass over here!');
  }
  else {
    if(value!=1) value--;
    else alert('Watch out! We\'ve got a badass over here!');
  }
  qt.innerText = value;
  total.innerText = '$'+(value*320); 
}

const product = document.querySelector('.product');

const moveBox = (val)=>{
  product.style.mozTransform =
  product.style.msTransform =
  product.style.webkitTransform =
  product.style.transform = 'translateX('+val+'px)';
}

const back = document.querySelector('.back');

const spring = new rebound.SpringSystem();

let animation = spring.createSpring(60,3);

animation.addListener({
  onSpringUpdate(spring){
    let current = spring.getCurrentValue();
    if(current > 1) spring.setEndValue(0);
    let val =  rebound.MathUtil.mapValueInRange(current,0,1,0,20);
    moveBox(val);
  }
})

back.addEventListener('click',()=>{
  animation.setEndValue(1);
});


const arrArr = Array.from(arrows);

arrArr.forEach(elem=>{
  elem.addEventListener('click',()=>{
    handleChange(elem);
  })
})