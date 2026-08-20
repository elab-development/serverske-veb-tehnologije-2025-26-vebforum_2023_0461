import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import * as topicsApi from "../api/topics";
import { ErrorMessage, LoadingSpinner } from "../components/StatusMessage";

export default function StatisticsPage() {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    topicsApi
      .topicStatistics()
      .then((res) => setRows(res.data))
      .catch(setError)
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="page">
      <h1>Statistika najaktivnijih tema</h1>
      <p className="muted">
        Deset tema sa najvišim skorom, sa brojem postova i komentara
        (agregirano preko spojenih tabela topics, categories, posts,
        comments i votes).
      </p>
      <ErrorMessage error={error} />
      {loading ? (
        <LoadingSpinner />
      ) : (
        <table className="data-table">
          <thead>
            <tr>
              <th>Tema</th>
              <th>Kategorija</th>
              <th>Postovi</th>
              <th>Komentari</th>
              <th>Skor</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row) => (
              <tr key={row.id}>
                <td>
                  <Link to={`/topics/${row.id}`}>{row.title}</Link>
                </td>
                <td>{row.category_name}</td>
                <td>{row.posts_count}</td>
                <td>{row.comments_count}</td>
                <td>{row.score}</td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </div>
  );
}
