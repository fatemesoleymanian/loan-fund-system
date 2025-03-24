<template>
<table>
  <tr>
    <th  v-for="(col,index) in columns" :key="index">
        {{ col.label }}
    </th>
  </tr>
  <tr  v-for="(row,index) in rows" :key="index">
    <td  v-for="(field,index) in row" :key="index">
      {{ field.label }}
    </td>
  </tr>
</table>
 </template>
 <script>

export default{
  props:{
    init_rows:{
      type:Array,
      default:()=>[]
    },
    init_columns:{
      type:Array,
      default:()=>[]
    }

  },
  setup(props){
    const columns = props.init_columns
    const rows = props.init_rows
    return{
    columns,
    rows
    }
  }
}
</script>
<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: center;
  padding: 8px;
}

tr {
  background-color: transparent;
}
tr:nth-child(1) {
  background-color: #dddddd;
}
</style>
