export default function (data){
    return `
    <div>Высота: ${data.altitude} м </div>
    <div>Рассеяная СР: ${data.diffuse} кВт*ч/м&sup2; </div>
    <div>Суммарная гориз. СР: ${data.full} кВт*ч/м&sup2; </div>
    <div>Суммарная СР при оптимальном угле: ${data.full_optimal} кВт*ч/м&sup2; </div>
    <div>Прямая нормальная СР: ${data.direct} кВт*ч/м&sup2; </div>
`;
}
