// Home interactivity: fetch /app/debug_db.php for live counts and update UI
async function fetchStats(){
  try{
    const res = await fetch('debug_db.php');
    if(!res.ok) throw new Error('Network');
    const j = await res.json();
    const counts = j.counts || {};
    document.querySelectorAll('.stat-number')[0].textContent = counts.pasien ?? 0;
    document.querySelectorAll('.stat-number')[1].textContent = counts.dokter ?? 0;
    document.querySelectorAll('.stat-number')[2].textContent = counts.obat ?? 0;
    document.querySelectorAll('.stat-number')[3].textContent = counts.appointment ?? 0;
    // update donut chart
    const total = (counts.pasien||0)+(counts.dokter||0)+(counts.obat||0)+(counts.appointment||0)||1;
    const a1 = Math.round((counts.pasien||0)/total*100);
    const a2 = Math.round((counts.dokter||0)/total*100);
    const a3 = Math.round((counts.obat||0)/total*100);
    const a4 = 100-(a1+a2+a3);
    const circles = document.querySelectorAll('.donut circle[data-i]');
    if(circles.length>=4){
      circles[0].setAttribute('stroke-dasharray', a1+' 100');
      circles[1].setAttribute('stroke-dasharray', a2+' 100');
      circles[2].setAttribute('stroke-dasharray', a3+' 100');
      circles[3].setAttribute('stroke-dasharray', a4+' 100');
    }
    // animate KPI
    document.querySelectorAll('.stat-number').forEach((el)=>{
      el.animate([{transform:'translateY(8px)',opacity:0},{transform:'translateY(0)',opacity:1}],{duration:400,easing:'cubic-bezier(.2,.9,.2,1)'});
    });
  }catch(e){console.error('home fetch',e)}
}

// initialize
fetchStats();
setInterval(fetchStats, 5000);

// click KPI to filter lists (simple behavior)
document.addEventListener('click', e=>{
  const kp = e.target.closest('.stat-card');
  if(kp){
    if(kp.textContent.includes('Pasien')) location.href='pasien.php';
    if(kp.textContent.includes('Dokter')) location.href='dokter.php';
    if(kp.textContent.includes('Obat')) location.href='obat.php';
    if(kp.textContent.includes('Janji')) location.href='appointment.php';
  }
});
