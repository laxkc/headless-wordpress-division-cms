import Link from "next/link";
import type { Article } from "@/lib/types";

export function ArticleCard({ article }: { article: Article }) {
  const date = new Date(article.publish_date);
  return (
    <Link
      href={`/insights/${article.slug}`}
      className="group block rounded-2xl border border-border bg-surface p-6 transition-shadow hover:shadow-lg"
    >
      <p className="text-xs text-muted">
        {date.toLocaleDateString("en-CA", {
          year: "numeric",
          month: "short",
          day: "numeric",
        })}
      </p>
      <h3 className="mt-2 text-lg font-semibold tracking-tight group-hover:text-primary transition-colors">
        {article.title}
      </h3>
      <p className="mt-2 text-sm text-muted line-clamp-3">
        {article.excerpt}
      </p>
    </Link>
  );
}
