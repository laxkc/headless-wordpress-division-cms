import type { Metadata } from "next";
import Link from "next/link";
import { getDivisions, getInsights } from "@/lib/api";
import { Container } from "@/components/Container";
import { ArticleCard } from "@/components/ArticleCard";
import { FilterChips } from "@/components/FilterChips";

export const metadata: Metadata = {
  title: "Insights",
  description:
    "Plain-language briefs from Northium advisors across all five practices.",
};

type SearchParams = {
  division?: string;
  page?: string;
};

export default async function InsightsPage({
  searchParams,
}: {
  searchParams: Promise<SearchParams>;
}) {
  const sp = await searchParams;
  const page = Math.max(1, Number(sp.page) || 1);
  const divisionFilter = sp.division || undefined;

  const [divisions, insights] = await Promise.all([
    getDivisions(),
    getInsights({ division: divisionFilter, page, per_page: 12 }),
  ]);

  const filterOptions = divisions.items.map((d) => ({
    value: d.slug,
    label: d.title,
  }));

  return (
    <Container className="py-20">
      <div className="max-w-2xl">
        <p className="text-xs uppercase tracking-[0.2em] text-muted">
          Insights
        </p>
        <h1 className="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">
          What we&apos;ve worked out, written down.
        </h1>
        <p className="mt-5 text-lg text-muted">
          Specific scenarios, not general thoughts. Filter by practice, or
          browse the full list. Every brief is written by the advisor who
          handled the question.
        </p>
      </div>

      <div className="mt-10">
        <FilterChips
          basePath="/insights"
          paramKey="division"
          active={divisionFilter}
          options={filterOptions}
          allLabel="All practices"
        />
      </div>

      {insights.items.length === 0 ? (
        <p className="mt-12 text-muted">
          No briefs yet for this practice.
        </p>
      ) : (
        <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {insights.items.map((article) => (
            <ArticleCard key={article.id} article={article} />
          ))}
        </div>
      )}

      {insights.total_pages > 1 ? (
        <Pagination
          page={insights.page}
          totalPages={insights.total_pages}
          divisionFilter={divisionFilter}
        />
      ) : null}
    </Container>
  );
}

function Pagination({
  page,
  totalPages,
  divisionFilter,
}: {
  page: number;
  totalPages: number;
  divisionFilter?: string;
}) {
  const buildHref = (target: number) => {
    const params = new URLSearchParams();
    if (divisionFilter) params.set("division", divisionFilter);
    if (target > 1) params.set("page", String(target));
    const qs = params.toString();
    return qs ? `/insights?${qs}` : "/insights";
  };

  return (
    <nav className="mt-12 flex items-center justify-between text-sm">
      {page > 1 ? (
        <Link
          href={buildHref(page - 1)}
          className="text-muted hover:text-foreground"
        >
          ← Previous
        </Link>
      ) : (
        <span />
      )}
      <span className="text-muted">
        Page {page} of {totalPages}
      </span>
      {page < totalPages ? (
        <Link
          href={buildHref(page + 1)}
          className="text-muted hover:text-foreground"
        >
          Next →
        </Link>
      ) : (
        <span />
      )}
    </nav>
  );
}
