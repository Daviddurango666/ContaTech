const ContaAPI=(function(){
  const BASE='/ContaTech/api/index.php?path=';
  function getToken(){return localStorage.getItem('contatech_token')||'';}
  function setSession(token,user){localStorage.setItem('contatech_token',token);localStorage.setItem('contatech_user',JSON.stringify(user));document.cookie='contatech_token='+token+'; path=/; max-age=28800; SameSite=Lax';}
  function clearSession(){localStorage.removeItem('contatech_token');localStorage.removeItem('contatech_user');document.cookie='contatech_token=; path=/; max-age=0';}
  function getUser(){try{return JSON.parse(localStorage.getItem('contatech_user')||'null');}catch{return null;}}
  async function request(path,options={}){
    const headers=Object.assign({'Content-Type':'application/json',Accept:'application/json'},options.headers||{});
    const token=getToken(); if(token) headers['Authorization']='Bearer '+token;
    const res=await fetch(BASE+path,{method:options.method||'GET',headers,body:options.body?JSON.stringify(options.body):undefined});
    const data=await res.json().catch(()=>({}));
    if(res.status===401){clearSession();if(!window.location.pathname.includes('/auth/')) window.location.href='/ContaTech/views/auth/login.php'; throw new Error(data.message||'No autorizado');}
    if(!data.success) throw new Error(data.message||'Error en la solicitud');
    return data;
  }
  return{getToken,setSession,clearSession,getUser,get:p=>request(p),post:(p,b)=>request(p,{method:'POST',body:b}),put:(p,b)=>request(p,{method:'PUT',body:b}),del:p=>request(p,{method:'DELETE'})};
})();
